import React, { createContext, useContext, useState, useEffect } from "react";

// Create context
const FormContext = createContext();

// Provider component
export const FormProvider = ({ children }) => {
  const [formData, setFormData] = useState(() => {
    // Load from localStorage on refresh
    const saved = localStorage.getItem("formData");
    return saved ? JSON.parse(saved) : {};
  });

  // Save automatically when formData changes
  useEffect(() => {
    localStorage.setItem("formData", JSON.stringify(formData));
  }, [formData]);

  const updateForm = (newData) => {
    setFormData((prev) => ({ ...prev, ...newData }));
  };

  return (
    <FormContext.Provider value={{ formData, updateForm }}>
      {children}
    </FormContext.Provider>
  );
};

// Hook to use it easily
export const useForm = () => useContext(FormContext);
