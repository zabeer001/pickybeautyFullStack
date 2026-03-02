import React from "react";

const baseButtonClassName =
  "rounded-2xl bg-[#d2346d] px-8 py-3 text-lg font-semibold text-white shadow-[0_10px_24px_rgba(210,52,109,0.28)] transition hover:bg-[#bb245f]";

export default function StepActions({
  backDisabled = false,
  backLabel = "\u2190 Zuruck",
  containerClassName = "mt-8 flex w-full max-w-md items-center justify-between gap-4",
  nextDisabled = false,
  nextLabel = "Weiter \u2192",
  nextType = "button",
  onBack,
  onNext,
  showBack = true,
}) {
  return (
    <div className={containerClassName}>
      {showBack ? (
        <button
          type="button"
          onClick={onBack}
          disabled={backDisabled}
          className={`${baseButtonClassName} disabled:cursor-not-allowed disabled:opacity-50`}
        >
          {backLabel}
        </button>
      ) : null}

      <button
        type={nextType}
        onClick={onNext}
        disabled={nextDisabled}
        className={`${baseButtonClassName} disabled:cursor-not-allowed disabled:opacity-50`}
      >
        {nextLabel}
      </button>
    </div>
  );
}
