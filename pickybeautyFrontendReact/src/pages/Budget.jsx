import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useBudgetStore } from "../stores/root/useHomeStore";


function Budget() {
  const [budget, setBudget] = useState("");
  const [error, setError] = useState("");
  const { setSelectedBudget } = useBudgetStore(); // ✅ Zustand setter
  const navigate = useNavigate();

  const presetBudgets = [100, 300, 500, 1000];

  const handleSelect = (amount) => {
    setBudget(amount);
    setError("");
  };

  const handleNext = () => {
    if (!budget || isNaN(budget) || Number(budget) <= 0) {
      setError("Bitte gib ein gültiges Budget ein!");
      return;
    }

    setSelectedBudget(budget); // ✅ save in Zustand
    setError("");
    navigate("/email");
  };

  return (
    <div className="h-[600px] flex flex-col justify-top items-center mt-[120px]">
      <h1 className="text-3xl mb-6 text-gray-800">Gib dein Budget an</h1>

      <div className="w-full max-w-md flex flex-col items-center">
        {/* Preset buttons */}
        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 w-full">
          {presetBudgets.map((amount) => (
            <button
              key={amount}
              onClick={() => handleSelect(amount)}
              className={`border rounded-xl p-3 text-lg font-medium transition-all duration-200 
                ${Number(budget) === amount
                  ? "bg-[#cc3366] text-white shadow-md"
                  : "bg-white text-gray-700 hover:!bg-pink-500 border-gray-300"
                }`}
            >
              {amount} €
            </button>
          ))}
        </div>

        {/* Manual input */}
        <input
          type="text"
          value={budget}
          onChange={(e) => setBudget(e.target.value.replace(/[^\d]/g, ""))}
          placeholder="Oder gib dein eigenes Budget ein..."
          className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 text-center"
        />

        {error && <p className="text-red-500 text-sm mt-2">{error}</p>}
      </div>

      <button
        onClick={handleNext}
        className="mt-6 bg-[#cc3366] text-white text-xl px-6 py-3 rounded-2xl shadow-md transition-all duration-300 hover:!bg-red-700 hover:!border-red-700"
      >
        Weiter →
      </button>
    </div>
  );
}

export default Budget;
