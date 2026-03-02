import React from "react";

export default function MultiStepShell({ children }) {
  return (
    <div className="min-h-screen flex flex-col items-center justify-start px-4 py-6 text-center">
      {children}
    </div>
  );
}
