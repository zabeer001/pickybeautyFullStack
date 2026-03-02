import React from "react";

export default function PaymentAmountCard({ amount }) {
  return (
    <div className="mt-6 w-full max-w-sm rounded-xl bg-[#ececf0] px-5 py-4 text-center text-slate-700 shadow-md">
      <span className="font-semibold">Gesamtbetrag:</span> {amount} €
    </div>
  );
}
