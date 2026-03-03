import React from "react";

export default function PaymentStatusPage({
  title,
  message,
  actionLabel,
  onAction,
  tone = "success",
}) {
  const accentClass =
    tone === "success"
      ? "border-emerald-200 bg-emerald-50 text-emerald-900"
      : "border-rose-200 bg-rose-50 text-rose-900";

  const buttonClass =
    tone === "success"
      ? "bg-emerald-600 hover:bg-emerald-700"
      : "bg-rose-600 hover:bg-rose-700";

  return (
    <div className="flex min-h-screen flex-col items-center justify-center px-4">
      <div
        className={`w-full max-w-md rounded-3xl border px-6 py-8 text-center shadow-sm ${accentClass}`}
      >
        <h1 className="text-2xl font-semibold">{title}</h1>
        <p className="mt-3 text-sm leading-6">{message}</p>
        <button
          type="button"
          onClick={onAction}
          className={`mt-6 inline-flex rounded-2xl px-5 py-3 text-sm font-semibold text-white transition ${buttonClass}`}
        >
          {actionLabel}
        </button>
      </div>
    </div>
  );
}
