import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import germanZipcodes from "../../../data/germanZipcodes.json";
import {
  useBudgetStore,
  useCategoryStore,
  useZipcodeStore,
  useOrderStore,
} from "../../../stores/root/useHomeStore";
import OrderFormFields from "./OrderFormFields";
import OrderHeader from "./OrderHeader";
import OrderSummaryCard from "./OrderSummaryCard";

function Order() {
  const { category } = useCategoryStore();
  const { zipcode } = useZipcodeStore();
  const { budget } = useBudgetStore();
  const { order, setOrderField } = useOrderStore();
  const navigate = useNavigate();
  const [addressSuggestions, setAddressSuggestions] = useState([]);

  useEffect(() => {
    if (!zipcode || order.x !== null || order.y !== null) {
      return;
    }

    const matchedLocation = germanZipcodes.find(
      (item) => `${item.zipcode} ${item.place}`.toLowerCase() === zipcode.toLowerCase()
    );

    if (matchedLocation) {
      setOrderField("x", Number(matchedLocation.latitude));
      setOrderField("y", Number(matchedLocation.longitude));
    }
  }, [zipcode, order.x, order.y, setOrderField]);

  const handleAddressChange = (event) => {
    const input = event.target.value.trimStart();
    setOrderField("address", input);

    if (input.length === 0) {
      setOrderField("x", null);
      setOrderField("y", null);
      setAddressSuggestions([]);
      return;
    }

    const filtered = germanZipcodes.filter((item) => {
      const zip = item.zipcode?.toString() || "";
      const place = item.place?.toLowerCase() || "";
      const normalizedInput = input.toLowerCase();

      return zip.startsWith(input) || place.includes(normalizedInput);
    });

    const exactMatch = filtered.find(
      (item) => `${item.zipcode} ${item.place}`.toLowerCase() === input.toLowerCase()
    );

    if (exactMatch) {
      setOrderField("x", Number(exactMatch.latitude));
      setOrderField("y", Number(exactMatch.longitude));
    } else {
      setOrderField("x", null);
      setOrderField("y", null);
    }

    setAddressSuggestions(filtered.slice(0, 10));
  };

  const handleAddressSelect = (item) => {
    setOrderField("address", `${item.zipcode} ${item.place}`);
    setOrderField("x", Number(item.latitude));
    setOrderField("y", Number(item.longitude));
    setAddressSuggestions([]);
  };

  const handleNext = (event) => {
    event.preventDefault();

    if (!order.name.trim() || !order.phone.trim() || !order.address.trim()) {
      window.alert("Bitte fülle alle Buchungsdetails aus.");
      return;
    }

    navigate("/payment");
  };

  return (
    <div className="h-[700px] flex flex-col justify-start items-center mt-[20px] px-4">
      <OrderHeader />
      <OrderSummaryCard budget={budget} category={category} zipcode={zipcode} />
      <form onSubmit={handleNext} className="w-full max-w-md flex flex-col space-y-4">
        <OrderFormFields
          order={order}
          setOrderField={setOrderField}
          addressSuggestions={addressSuggestions}
          onAddressChange={handleAddressChange}
          onAddressSelect={handleAddressSelect}
        />
        <StepActions
          containerClassName="mt-6 flex items-center justify-between gap-4"
          nextType="submit"
          onBack={() => navigate("/email")}
        />
      </form>
    </div>
  );
}

export default Order;
