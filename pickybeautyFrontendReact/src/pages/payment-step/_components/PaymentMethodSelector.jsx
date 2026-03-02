import React from "react";

function PaymentMethodOption({ checked, label, onChange }) {
  return (
    <label className="flex items-center gap-3 rounded-2xl border border-[#c8cdd8] bg-transparent px-4 py-4 text-left text-xl text-[#0f2748]">
      <input
        type="checkbox"
        checked={checked}
        onChange={onChange}
        className="h-5 w-5"
      />
      <span>{label}</span>
    </label>
  );
}

export default function PaymentMethodSelector({
  options,
  selectedPaymentMethod,
  onSelect,
}) {
  return (
    <div className="mt-6 flex w-full max-w-sm flex-col gap-4">
      {options.map((option) => (
        <PaymentMethodOption
          key={option.value}
          checked={selectedPaymentMethod === option.value}
          label={option.label}
          onChange={() => onSelect("paymentMethod", option.value)}
        />
      ))}
    </div>
  );
}
