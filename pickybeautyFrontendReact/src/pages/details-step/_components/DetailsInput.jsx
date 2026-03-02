import React from "react";

export default function DetailsInput({ onChange, value }) {
  return (
    <textarea
      value={value}
      onChange={onChange}
      placeholder="Zum Beispiel: Natürliches Make-up für eine Hochzeit..."
      rows={5}
      className="mt-8 min-h-52 w-full rounded-2xl border border-[#c8c6bf] bg-[#f4e8c9] px-5 py-4 text-2xl text-slate-900 outline-none transition focus:border-[#d2346d] focus:ring-2 focus:ring-[#f3b4c9]"
    />
  );
}
