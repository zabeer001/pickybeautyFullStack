import React from "react";

export default function PaymentHeader() {
  return (
    <>
      <h1 className="mt-10 text-3xl text-[#0f2748] font-semibold">
        Zahlungsart auswahlen
      </h1>
      <p className="mt-3 text-sm italic text-slate-600">
        Barzahlung oder Online-Zahlung
      </p>
    </>
  );
}
