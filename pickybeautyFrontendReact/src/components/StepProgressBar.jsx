import React from "react";
import { useLocation } from "react-router-dom";

export default function StepProgressBar() {
  const steps = [
    "Kategorie",
    "Standort",
    "Budget",
    "E-Mail",
    "Bestellung",
  ];

  const location = useLocation();
  const pathname = location.pathname;

  // Map each route path to a step index (1-based)
  const pathToStep = {
    "/": 1,
    "/location": 2,
    "/budget": 3,
    "/email": 4,
    "/order": 5,
  };

  // Determine the current step based on pathname
  const currentStep = pathToStep[pathname] || 1;

  return (
    <div className="w-full max-w-2xl mx-auto mb-8 flex items-center justify-between relative">
      {steps.map((label, index) => (
        <div key={index} className="flex-1 flex flex-col items-center relative">
          {/* Circle */}
          <div
            className={`w-8 h-8 flex items-center justify-center rounded-full border-2 z-10 transition-colors duration-300 ${index + 1 <= currentStep
                ? "bg-[#cc3366] border-[#cc3366] text-white"
                : "bg-white border-gray-300 text-gray-400"
              }`}
          >
            {index + 1}
          </div>

          {/* Label */}
          <p
            className={`mt-2 text-xs font-medium ${index + 1 <= currentStep ? "text-[#cc3366]" : "text-gray-400"
              }`}
          >
            {label}
          </p>

          {/* Connector line */}
          {index < steps.length - 1 && (
            <div
              className={`absolute top-4 left-[155%] w-full h-1 -translate-x-1/2 ${index + 1 < currentStep ? "bg-[#cc3366]" : "bg-gray-200"
                }`}
            ></div>
          )}
        </div>
      ))}
    </div>
  );
}
