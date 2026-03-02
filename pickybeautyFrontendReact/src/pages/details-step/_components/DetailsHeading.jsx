import React from "react";

export default function DetailsHeading() {
  return (
    <>
      <div className="w-full max-w-4xl text-left">
        <p className="text-[28px] font-medium text-slate-900">Bestellung</p>
      </div>
      <div className="w-full max-w-2xl">
        <h1 className="text-5xl font-semibold text-[#0f2748]">Was hast du im Sinn?</h1>
        <p className="mt-4 text-2xl italic text-slate-600">
          Schreibe kurz, was du dir wünschst.
        </p>
      </div>
    </>
  );
}
