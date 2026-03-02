import React from "react";

export default function OrderSummaryCard({ budget, category, zipcode }) {
  return (
    <div className="bg-white/60 p-4 rounded-xl mb-6 shadow-md text-gray-700 w-full max-w-md text-left">
      <p><strong>Kategorie:</strong> {category || "—"}</p>
      <p><strong>Standort:</strong> {zipcode || "—"}</p>
      <p><strong>Budget:</strong> {budget ? `${budget} €` : "—"}</p>
    </div>
  );
}
