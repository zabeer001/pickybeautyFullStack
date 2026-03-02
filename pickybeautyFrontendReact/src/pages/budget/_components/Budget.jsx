import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import { useBudgetStore } from "../../../stores/root/useHomeStore";
import BudgetHeader from "./BudgetHeader";
import BudgetInput from "./BudgetInput";
import BudgetPresetGrid from "./BudgetPresetGrid";


function Budget() {
  const [budget, setBudget] = useState("");
  const [error, setError] = useState("");
  const { setSelectedBudget } = useBudgetStore(); // ✅ Zustand setter
  const navigate = useNavigate();

  const presetBudgets = [100, 300, 500, 1000];

  const handleSelect = (amount) => {
    setBudget(amount);
    setError("");
  };

  const handleNext = () => {
    if (!budget || isNaN(budget) || Number(budget) <= 0) {
      setError("Bitte gib ein gültiges Budget ein!");
      return;
    }

    setSelectedBudget(budget); // ✅ save in Zustand
    setError("");
    navigate("/email");
  };

  return (
    <div className="h-[700px] flex flex-col justify-start items-center mt-[20px] px-4">
      <BudgetHeader />
      <div className="w-full max-w-md flex flex-col items-center">
        <BudgetPresetGrid
          budget={budget}
          onSelect={handleSelect}
          presetBudgets={presetBudgets}
        />
        <BudgetInput
          error={error}
          onChange={(event) => setBudget(event.target.value.replace(/[^\d]/g, ""))}
          value={budget}
        />
      </div>
      <StepActions
        onBack={() => navigate("/location")}
        onNext={handleNext}
      />
    </div>
  );
}

export default Budget;
