import React from "react";
import { HashRouter as Router, Routes, Route } from "react-router-dom"; // ✅ changed here
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

import CartPage from "./pages/cart-step/CartPage";
import CategoryPage from "./pages/category-step/CategoryPage";
import DetailsStepPage from "./pages/details-step/DetailsStepPage";
import LocationStepPage from "./pages/location-step/LocationStepPage";
import MultiStepFormPage from "./pages/multi-step-form/MultiStepFormPage";
import BudgetPage from "./pages/budget-step/BudgetPage";
import PaymentStepPage from "./pages/payment-step/PaymentStepPage";

import { FormProvider } from "./context/FormContext";
import OrderPage from "./pages/order-step/OrderPage";
import EmailStepPage from "./pages/email-step/EmailStepPage";
import { ToastContainer } from "react-toastify";

const queryClient = new QueryClient();

export default function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <FormProvider>
        <Router> {/* ✅ Now using HashRouter */}
          <Routes>
            <Route path="/" element={<MultiStepFormPage />}>
              <Route index element={<DetailsStepPage />} />
              <Route path="category" element={<CategoryPage />} />
              <Route path="location" element={<LocationStepPage />} />
              <Route path="budget" element={<BudgetPage />} />
              <Route path="cart" element={<CartPage />} />
           
              <Route path="email" element={<EmailStepPage />} />
              <Route path="order" element={<OrderPage />} />
              <Route path="payment" element={<PaymentStepPage />} />
            </Route>
          </Routes>
          <ToastContainer position="top-right" autoClose={3000} />
        </Router>
      </FormProvider>
    </QueryClientProvider>
  );
}
