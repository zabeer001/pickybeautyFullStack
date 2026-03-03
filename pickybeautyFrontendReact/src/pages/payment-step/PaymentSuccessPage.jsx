import React, { useEffect } from "react";
import { useNavigate } from "react-router-dom";
import PaymentStatusPage from "./_components/PaymentStatusPage";
import { useOrderStore } from "../../stores/root/useHomeStore";

export default function PaymentSuccessPage() {
  const navigate = useNavigate();
  const { resetOrder } = useOrderStore();

  useEffect(() => {
    resetOrder();
  }, [resetOrder]);

  return (
    <PaymentStatusPage
      title="Zahlung erfolgreich"
      message="Die Zahlung wurde erfolgreich abgeschlossen."
      actionLabel="Zur Startseite"
      onAction={() => navigate("/")}
      tone="success"
    />
  );
}
