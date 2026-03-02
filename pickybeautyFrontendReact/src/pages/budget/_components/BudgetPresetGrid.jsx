import React from "react";

export default function BudgetPresetGrid({ budget, onSelect, presetBudgets }) {
  return (
    <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 w-full">
      {presetBudgets.map((amount) => (
        <button
          key={amount}
          onClick={() => onSelect(amount)}
          className={`border rounded-xl p-3 text-lg font-medium transition-all duration-200 ${
            Number(budget) === amount
              ? "bg-[#cc3366] text-white shadow-md"
              : "bg-white text-gray-700 hover:!bg-pink-500 border-gray-300"
          }`}
        >
          {amount} €
        </button>
      ))}
    </div>
  );
}
