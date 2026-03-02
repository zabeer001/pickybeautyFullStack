import React from "react";

export default function BudgetInput({ error, onChange, value }) {
  return (
    <>
      <input
        type="text"
        value={value}
        onChange={onChange}
        placeholder="Oder gib dein eigenes Budget ein..."
        className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 text-center"
      />
      {error && <p className="text-red-500 text-sm mt-2">{error}</p>}
    </>
  );
}
