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

function buildCheckoutReturnUrl(routePath, search = "") {
  const baseUrl = `${window.location.origin}${window.location.pathname}`;
  return `${baseUrl}#${routePath}${search}`;
}

function toStripeAmount(amount) {
  const numericAmount = Number(amount);

  if (!Number.isFinite(numericAmount) || numericAmount <= 0) {
    return 0;
  }

  return Math.round(numericAmount * 100);
}

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
      x: order.x,
      y: order.y,
      shipping: {
        name: order.name,
        email: order.email,
        phone: order.phone,
        zip_code: zipcode,
        address: order.address,
        x: order.x,
        y: order.y,
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
        if (order.paymentMethod === "online") {
          const stripeAmount = toStripeAmount(discountBudget || budget);

          if (!stripeAmount) {
            throw new Error("Der Zahlungsbetrag ist ungueltig.");
          }

          const checkoutResponse = await fetch(
            `${WP_BACKEND}/wp-json/kibsterlp-admin/v1/stripe/checkout`,
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                amount: stripeAmount,
                currency: "eur",
                order_id: data?.data?.id,
                email: order.email,
                success_url: buildCheckoutReturnUrl(
                  "/payment-success",
                  "?session_id={CHECKOUT_SESSION_ID}"
                ),
                cancel_url: buildCheckoutReturnUrl(
                  "/payment-failed",
                  "?checkout=cancelled"
                ),
              }),
            }
          );
          const checkoutData = await checkoutResponse.json();

          if (!checkoutResponse.ok || !checkoutData?.url) {
            throw new Error(
              checkoutData?.message || "Stripe checkout could not be started."
            );
          }

          window.location.assign(checkoutData.url);
          return;
        }

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
      console.error("Payment submission error:", error);
      toast.error(error.message || "Server error. Please try again later.", {
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
