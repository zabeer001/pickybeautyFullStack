import React from "react";
import { Outlet } from "react-router-dom";
import StepProgressBar from "../../../components/StepProgressBar";
import MultiStepBrand from "./MultiStepBrand";
import MultiStepShell from "./MultiStepShell";

export default function MultiStepForm() {
  return (
    <MultiStepShell>
      <StepProgressBar />
      <MultiStepBrand />
      <Outlet />
    </MultiStepShell>
  );
}
