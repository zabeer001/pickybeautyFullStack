import React from "react";
import { Outlet, useNavigate } from "react-router-dom";
import StepProgressBar from "../components/StepProgressBar";

export default function MultiStepForm() {
  const navigate = useNavigate();

  const handleBack = () => {
    navigate(-1); // 👈 goes one step back in history
  };

  return (
    <div className="min-h-screen bg-gradient-to-b from-pink-50 to-white flex flex-col items-center justify-center px-4 py-8 text-center">
      {/* Back Button */}
      <div className="w-full max-w-2xl flex justify-start mb-4">
        <button
          onClick={handleBack}
          className="flex items-center hover:!text-white hover:!bg-red-700 font-medium transition-all duration-200 ml-[60px] p-2 !bg-black !text-white !border border-black hover:!border-red-700 mb-5 rounded-2xl"
        >
          ← Zurück
        </button>
      </div>

      <StepProgressBar />
      <Outlet />
    </div>
  );
}
