import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import {
  useBudgetStore,
  useCategoryStore,
  useZipcodeStore,
  useOrderStore,
} from "../../../stores/root/useHomeStore.js";
import { WP_BACKEND } from "../../../../env.js";
import { toast } from "react-toastify";
import PaymentAmountCard from "./PaymentAmountCard";
import PaymentHeader from "./PaymentHeader";
import PaymentMethodSelector from "./PaymentMethodSelector";

const paymentOptions = [
  { label: "Barzahlung", value: "cash" },
  { label: "Online-Zahlung", value: "online" },
];

export default function PaymentStep() {
  const navigate = useNavigate();
  const { category } = useCategoryStore();
  const { zipcode } = useZipcodeStore();
  const { budget } = useBudgetStore();
  const { order, setOrderField, resetOrder } = useOrderStore();

  const [discountBudget, setDiscountBudget] = useState(budget);
  const [submitting, setSubmitting] = useState(false);
  const categoryId =
    typeof category === "object" && category !== null ? category.id ?? null : category;

  useEffect(() => {
    const fetchDiscount = async () => {
      if (!order.email || !budget) {
        return;
      }

      try {
        const response = await fetch(
          `${WP_BACKEND}/wp-json/kibsterlp-admin/v1/order-discount`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: order.email,
              budget,
            }),
          }
        );

        const data = await response.json();
        setDiscountBudget(data?.data?.discounted_budget ?? budget);
      } catch (error) {
        console.error("Discount API error:", error);
        setDiscountBudget(budget);
      }
    };

    fetchDiscount();
  }, [budget, order.email]);

  const handleSubmit = async () => {
    if (!order.paymentMethod) {
      window.alert("Bitte wähle eine Zahlungsart aus.");
      return;
    }

    setSubmitting(true);

    const finalOrder = {
      category_id: categoryId,
      budget: discountBudget,
      payment_method: order.paymentMethod,
      shipping: {
        name: order.name,
        email: order.email,
        phone: order.phone,
        zip_code: zipcode,
        address: order.address,
      },
    };

    try {
      const response = await fetch(`${WP_BACKEND}/wp-json/kibsterlp-admin/v1/orders`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(finalOrder),
      });

      const data = await response.json();

      if (data.status === true) {
        resetOrder();
        toast.success(data.message || "Order placed successfully!", {
          position: "top-right",
          autoClose: 3000,
          onClose: () => navigate("/"),
        });
        return;
      }

      toast.error(data.message || "Something went wrong!", {
        position: "top-right",
        autoClose: 3000,
      });
    } catch (error) {
      console.error("Error saving order:", error);
      toast.error("Server error. Please try again later.", {
        position: "top-right",
        autoClose: 3000,
      });
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="h-[720px] flex flex-col items-center mt-[20px] px-4">
      <PaymentHeader />
      <PaymentAmountCard amount={discountBudget || budget || 0} />
      <PaymentMethodSelector
        options={paymentOptions}
        selectedPaymentMethod={order.paymentMethod}
        onSelect={setOrderField}
      />
      <StepActions
        containerClassName="mt-8 flex w-full max-w-sm items-center justify-between gap-4"
        nextDisabled={submitting}
        nextLabel={submitting ? "Senden..." : "Bestellung abschicken →"}
        onBack={() => navigate("/order")}
        onNext={handleSubmit}
      />
    </div>
  );
}
