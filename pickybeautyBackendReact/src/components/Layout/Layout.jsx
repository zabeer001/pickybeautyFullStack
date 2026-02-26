import React, { useEffect, useState } from "react";
import Sidebar from "./Sidebar";
import { Outlet } from "react-router-dom";

const Layout = () => {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const storedUser = localStorage.getItem("auth_user");
    if (!storedUser) {
      setUser(null);
      return;
    }

    try {
      setUser(JSON.parse(storedUser));
    } catch (error) {
      console.error("Unable to parse auth_user from storage:", error);
      setUser(null);
    }
  }, []);

  return (
    <div className="relative min-h-screen bg-gradient-to-br from-white via-slate-50 to-slate-100 text-slate-900">
      {/* background glows */}
      <div className="pointer-events-none absolute inset-0 overflow-hidden">
        <div className="absolute -left-16 top-0 h-64 w-64 rounded-full bg-cyan-200/40 blur-[140px]" />
        <div className="absolute right-0 top-32 h-72 w-72 rounded-full bg-indigo-200/40 blur-[180px]" />
        <div className="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-amber-100/60 blur-[160px]" />
      </div>

      <div className="relative z-10 mx-auto flex min-h-screen w-full max-w-[1800px] flex-col lg:flex-row">
        <Sidebar user={user} />

        <main className="flex-1 overflow-y-auto px-4 py-6 sm:px-8 lg:px-12">
          <div className="mx-auto w-full max-w-7xl space-y-8">
            <section className="relative">
              <Outlet />
            </section>
          </div>
        </main>
      </div>
    </div>
  );
};

export default Layout;
