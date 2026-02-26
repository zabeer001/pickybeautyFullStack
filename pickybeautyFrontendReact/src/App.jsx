import React from "react";
import { HashRouter as Router, Routes, Route } from "react-router-dom"; // ✅ changed here
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

import Cart from "./pages/Cart";
import Category from "./pages/Category";
import MultiStepForm from "./pages/MultiStepForm";
import Budget from "./pages/Budget";
import Location from "./pages/Location";
import { FormProvider } from "./context/FormContext";
import Order from "./pages/Order";
import EmailStep from "./pages/EmailStep";
import { ToastContainer } from "react-toastify";

const queryClient = new QueryClient();

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <FormProvider>
        <Router> {/* ✅ Now using HashRouter */}
          <Routes>
            <Route path="/" element={<MultiStepForm />}>
              <Route index element={<Category />} />
              <Route path="budget" element={<Budget />} />
              <Route path="cart" element={<Cart />} />
              <Route path="location" element={<Location />} />
              <Route path="email" element={<EmailStep />} />
              <Route path="order" element={<Order />} />
            </Route>
          </Routes>
          <ToastContainer position="top-right" autoClose={3000} />
        </Router>
      </FormProvider>
    </QueryClientProvider>
  );
}
