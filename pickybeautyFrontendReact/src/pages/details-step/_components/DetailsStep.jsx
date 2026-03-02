import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import { useForm } from "../../../context/FormContext";
import DetailsHeading from "./DetailsHeading";
import DetailsInput from "./DetailsInput";

export default function DetailsStep() {
  const navigate = useNavigate();
  const { formData, updateForm } = useForm();
  const [details, setDetails] = useState(formData.details || "");

  const handleNext = () => {
    updateForm({ details: details.trim() });
    navigate("/category");
  };

  return (
    <div className="flex min-h-[620px] w-full flex-col items-center px-4 pb-10 pt-2 text-center">
      <DetailsHeading />
      <div className="mt-6 w-full max-w-2xl">
        <DetailsInput
          value={details}
          onChange={(event) => setDetails(event.target.value)}
        />
        <StepActions
          containerClassName="mt-10 flex w-full justify-end"
          nextLabel="Weiter →"
          onNext={handleNext}
          showBack={false}
        />
      </div>
    </div>
  );
}
