import React from "react";
import { useNavigate } from "react-router-dom";
import PaymentStatusPage from "./_components/PaymentStatusPage";

export default function PaymentFailurePage() {
  const navigate = useNavigate();

  return (
    <PaymentStatusPage
      title="Zahlung fehlgeschlagen"
      message="Die Zahlung ist fehlgeschlagen."
      actionLabel="Zur Zahlungsseite"
      onAction={() => navigate("/payment", { replace: true })}
      tone="failure"
    />
  );
}
