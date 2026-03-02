import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import { useOrderStore } from "../../../stores/root/useHomeStore";
import EmailHeader from "./EmailHeader";
import EmailInput from "./EmailInput";

export default function EmailStep() {
  const { order, setOrderField } = useOrderStore();
  const navigate = useNavigate();
  const [email, setEmail] = useState(order.email || "");
  const [error, setError] = useState("");

  const validateEmail = (value) =>
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());

  const handleNext = () => {
    const trimmed = email.trim();
    if (!validateEmail(trimmed)) {
      setError("Bitte gib eine gültige E-Mail-Adresse an.");
      return;
    }

    setOrderField("email", trimmed);
    setError("");
    navigate("/order");
  };

  const handleBack = () => {
    navigate("/budget");
  };

  return (
    <div className="h-[600px] flex flex-col justify-start items-center mt-[20px] text-center px-4">
      <EmailHeader />
      <EmailInput
        error={error}
        onChange={(event) => {
          setEmail(event.target.value);
          setError("");
        }}
        value={email}
      />
      <StepActions
        containerClassName="mt-10 flex w-full max-w-md items-center justify-between gap-4"
        onBack={handleBack}
        onNext={handleNext}
      />
    </div>
  );
}
