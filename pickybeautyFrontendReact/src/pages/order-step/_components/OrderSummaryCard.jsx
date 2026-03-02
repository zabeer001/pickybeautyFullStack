import React from "react";

export default function OrderSummaryCard({ budget, category, zipcode }) {
  const categoryLabel =
    typeof category === "object" && category !== null
      ? category.title || category.id || "—"
      : category || "—";

  return (
    <div className="bg-white/60 p-4 rounded-xl mb-6 shadow-md text-gray-700 w-full max-w-md text-left">
      <p><strong>Kategorie:</strong> {categoryLabel}</p>
      <p><strong>Standort:</strong> {zipcode || "—"}</p>
      <p><strong>Budget:</strong> {budget ? `${budget} €` : "—"}</p>
    </div>
  );
}
