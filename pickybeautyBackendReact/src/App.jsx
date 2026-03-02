import React, { useEffect, useState } from "react";
import { HashRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import Layout from "./components/Layout/Layout";
import OrderIndexPage from "./pages/orders/OrderIndexPage";
import CategoriesIndexPage from "./pages/categories/CategoriesIndexPage";
import UsersIndexPage from "./pages/users/UsersIndexPage";
import OrderViewPage from "./pages/orders/OrderViewPage";
import OrderEditPage from "./pages/orders/OrderEditPage";
import { ToastContainer } from "react-toastify";
import VendorMyOrderPage from "./pages/orders/VendorMyOrderPage";
import UserMyOrderPage from "./pages/orders/UserMyorderPgae";
import CategoriesCreatePage from "./pages/categories/CategoriesCreatePage";
import CategoriesEditPage from "./pages/categories/CategoriesEditPage";
import LoyaltyIndexPage from "./pages/loyalty/LoyaltyIndexPage";
import CustomersIndexPage from "./pages/customers/CustomersIndexPage";
import CustomersCreatePage from "./pages/customers/CustomersCreatePage";
import CustomersEditPage from "./pages/customers/CustomersEditPage";
import UserLocationPage from "./pages/location/UserLocationPage";

import LoyaltyCreatePgae from "./pages/loyalty/LoyaltyCreatePgae";
import LoyaltyEditPage from './pages/loyalty/LoyaltyEditPage';

// The function closure and repeated code at the end of the original file
// has been removed/corrected here.

function App() {
  const [role, setRole] = useState(null);
  const [token, setToken] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // ✅ Get stored token and user info
    const storedToken = localStorage.getItem("jwt_token");
    const storedUser = JSON.parse(localStorage.getItem("auth_user") || "{}");

    // ✅ Extract first role safely from array
    const userRole = storedUser?.roles?.[0] || null;

    setToken(storedToken);
    setRole(userRole);
    setLoading(false);
  }, []);

  if (loading) {
    return (
      <div className="!flex !h-screen !items-center !justify-center !text-gray-500 !text-sm">
        Loading...
      </div>
    );
  }

  // 🔒 Redirect to login if not logged in
  const loginPath = "/sign-in";
  const isBrowser = typeof window !== "undefined";
  const isAlreadyOnLogin =
    isBrowser && window.location.pathname === loginPath;

  if (!token) {
    if (isBrowser && !isAlreadyOnLogin) {
      window.location.replace(loginPath); // avoid SPA loop by only redirecting once
    }

    return (
      <div className="!flex !h-screen !items-center !justify-center !text-gray-500 !text-sm">
        Redirecting to login...
      </div>
    );
  }

  return (
    <Router>
      <Routes>
        <Route path="/" element={<Layout />}>
          <Route path="admin/orders/:uniq_id" element={<OrderViewPage />} />
          <Route path="location" element={<UserLocationPage />} />

          {role === "vendor" ? (
            <Route index element={<Navigate to="/vendor/my-orders" replace />} />
          ) : role === "administrator" ? (
            <Route index element={<OrderIndexPage />} />
          ) : (
            <Route index element={<Navigate to="/my-orders" replace />} />
          )}

          {/* Admin routes (only for administrator) */}
          {role === "administrator" && (
            <>
              <Route path="admin/users" element={<UsersIndexPage />} />
              <Route path="admin/orders" element={<OrderIndexPage />} />
              <Route path="admin/orders/edit/:uniq_id" element={<OrderEditPage />} />
              <Route path="admin/categories" element={<CategoriesIndexPage />} />
              <Route path="admin/categories/create" element={<CategoriesCreatePage />} />
              <Route path="admin/categories/edit/:id" element={<CategoriesEditPage />} />
              <Route path="admin/customers" element={<CustomersIndexPage />} />
              <Route path="admin/customers/create" element={<CustomersCreatePage />} />
              <Route path="admin/customers/edit/:id" element={<CustomersEditPage />} />
              <Route path="admin/loyalty" element={<LoyaltyIndexPage />} />
              <Route path="admin/loyalty/create" element={<LoyaltyCreatePgae />} />
              <Route path="admin/loyalty/edit/:id" element={<LoyaltyEditPage />} />
            </>
          )}

          {/* Vendor routes */}
          {role === "vendor" && (
            <Route path="vendor/my-orders" element={<VendorMyOrderPage />} />
          )}

          {/* User (subscriber) routes */}
          {role !== "vendor" && role !== "administrator" && (
            <Route path="my-orders" element={<UserMyOrderPage />} />
          )}

          {/* Fallbacks for unauthorized routes */}
          {role !== "vendor" && (
            <Route path="vendor/*" element={<Navigate to="/" replace />} />
          )}
          {role !== "administrator" && (
            <Route path="admin/*" element={<Navigate to="/" replace />} />
          )}
        </Route>
      </Routes>

      <ToastContainer position="top-right" autoClose={3000} />
    </Router>
  );
}

export default App;
