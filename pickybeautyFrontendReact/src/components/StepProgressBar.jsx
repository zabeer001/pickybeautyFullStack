import React from "react";
import { useLocation } from "react-router-dom";

export default function StepProgressBar() {
  const steps = [
    "Details",
    "Kategorie",
    "Standort",
    "Budget",
    "E-Mail",
    "Buchungs-Details",
    "Zahlung",
  ];

  const location = useLocation();
  const pathname = location.pathname;

  // Map each route path to a step index (1-based)
  const pathToStep = {
    "/": 1,
    "/category": 2,
    "/location": 3,
    "/cart": 3,
    "/budget": 4,
    "/email": 5,
    "/order": 6,
    "/payment": 7,
  };

  // Determine the current step based on pathname
  const currentStep = pathToStep[pathname] || 1;
  const progressRatio = (currentStep - 1) / (steps.length - 1);
  const edgeOffsetPercent = 100 / (steps.length * 2);
  const trackInset = `${edgeOffsetPercent}%`;
  const trackWidth = `calc(100% - ${edgeOffsetPercent * 2}%)`;

  return (
    <div className="mx-auto mb-10 w-full max-w-5xl px-2">
      <div className="!relative flex items-start justify-between">
        <div
          className="!absolute top-5 h-[2px] !bg-[#d6deea]"
          style={{ left: trackInset, right: trackInset }}
        />
        <div
          className="!absolute top-5 h-[2px] !bg-[#d2346d] transition-all duration-300"
          style={{
            left: trackInset,
            width: `calc(${trackWidth} * ${progressRatio})`,
          }}
        />

        {steps.map((label, index) => (
          <div key={index} className="relative z-10 flex flex-1 flex-col items-center">
            <div
              className={`!relative z-10 flex !h-10 !w-10 items-center justify-center rounded-full !border-2 text-sm font-semibold transition-colors duration-300 ${
                index + 1 <= currentStep
                  ? "!border-[#d2346d] !bg-[#d2346d] !text-white"
                  : "!border-[#bfc8d5] !bg-white !text-[#9aa6b8]"
              }`}
            >
              {index + 1}
            </div>

            <p
              className={`mt-3 text-center text-xs font-semibold ${
                index + 1 <= currentStep ? "text-[#d2346d]" : "text-[#98a3b5]"
              }`}
            >
              {label}
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}
