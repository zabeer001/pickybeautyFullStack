import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useOrderStore } from "../stores/root/useHomeStore";

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
    <div className="h-[600px] flex flex-col justify-top items-center mt-[120px] text-center px-4">
      <h1 className="text-3xl mb-3 text-gray-800 font-semibold">
        Deine E-Mail-Adresse
      </h1>
      <p className="text-gray-600 mb-6 max-w-xl">
        Damit wir dich erreichen können, brauchen wir noch eine gültige
        E-Mail-Adresse. Sie wird automatisch in die finale Bestellung übernommen.
      </p>

      <div className="w-full max-w-md flex flex-col gap-3">
        <input
          type="email"
          value={email}
          onChange={(event) => {
            setEmail(event.target.value);
            setError("");
          }}
          placeholder="name@beispiel.de"
          className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 text-center"
        />
        {error && <p className="text-red-500 text-sm">{error}</p>}
      </div>

      <div className="flex gap-3 mt-10">
       
        <button
          type="button"
          onClick={handleNext}
          className="px-6 py-3 rounded-2xl bg-[#cc3366] text-white shadow-md transition-all duration-300 hover:!bg-red-700"
        >
          Weiter →
        </button>
      </div>
    </div>
  );
}
