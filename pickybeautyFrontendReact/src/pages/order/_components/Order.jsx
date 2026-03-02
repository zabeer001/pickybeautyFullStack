import React from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
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
