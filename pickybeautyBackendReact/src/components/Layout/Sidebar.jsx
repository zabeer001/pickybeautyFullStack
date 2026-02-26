import React from "react";
import { NavLink } from "react-router-dom";
import {
  BadgeCheck,
  Layers3,
  LayoutDashboard,
  PackageCheck,
  UserRoundCog,
} from "lucide-react";

const Sidebar = ({ user }) => {
  const isVendor = user?.roles?.includes("vendor");
  const isAdmin = user?.roles?.includes("administrator");
   const isUser = !isVendor && !isAdmin;

  const adminLinks = [
    {
      to: "/admin/orders",
      label: "Orders",
      description: "All customer requests",
      icon: PackageCheck,
    },
    {
      to: "/admin/categories",
      label: "Categories",
      description: "Structure your services",
      icon: Layers3,
    },
    {
      to: "/admin/customers",
      label: "Customers",
      description: "Manage customers",
      icon: UserRoundCog,
    },

    {
      to: "/admin/loyalty",
      label: "Loyalty",
      description: "Structure your services",
      icon: Layers3,
    },
    // {
    //   to: "/admin/users",
    //   label: "Users",
    //   description: "Manage vendor access",
    //   icon: UserRoundCog,
    // },
  ];

  const vendorLinks = [
    {
      to: "/vendor/my-orders",
      label: "My Orders",
      description: "Requests assigned to you",
      icon: BadgeCheck,
    },
  ];

  const userLinks = [
    {
      to: "/my-orders",
      label: "My Orders",
      description: "Your previous orders",
      icon: PackageCheck,
    },
  ];

  const renderLinks = (links, sectionLabel) => (
    <>
      <p className="text-xs uppercase tracking-[0.3em] text-slate-400">{sectionLabel}</p>
      <nav className="space-y-2">
        {links.map(({ to, label, description, icon: Icon }) => (
          <NavLink
            key={to}
            to={to}
            className={({ isActive }) =>
              [
                "group flex items-center justify-between rounded-2xl border px-4 py-3 transition-all duration-200",
                isActive
                  ? "border-slate-200 bg-white text-slate-900 shadow-lg shadow-slate-200"
                  : "border-transparent text-slate-500 hover:border-slate-200 hover:bg-white",
              ].join(" ")
            }
          >
            <div className="flex items-center gap-3">
              <span className="rounded-xl border border-slate-200 bg-slate-50 p-2 text-slate-500 group-hover:text-slate-900">
                <Icon className="h-5 w-5" />
              </span>
              <div>
                <p className="text-sm font-semibold">{label}</p>
                <p className="text-xs text-slate-500">{description}</p>
              </div>
            </div>
            <span className="text-[10px] uppercase tracking-[0.35em] text-slate-400" aria-hidden="true">
              →
            </span>
          </NavLink>
        ))}
      </nav>
    </>
  );

  const loadingState = (
    <div className="w-full animate-pulse rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
      <div className="h-4 w-32 rounded bg-white" />
      <div className="mt-2 h-3 w-48 rounded bg-white" />
    </div>
  );

  return (
    <aside className="w-full border-b border-white px-4 py-5 shadow-2xl shadow-slate-200/60 backdrop-blur lg:w-72 lg:border-b-0 lg:border-r">
      <div className="flex flex-col gap-6">
        <div className="rounded-2xl border border-white bg-white px-5 py-6 shadow-inner shadow-slate-100">
          <div className="mb-4 flex items-center gap-3 text-slate-900">
            <span className="rounded-2xl bg-slate-900/5 p-3 text-slate-900">
              <LayoutDashboard className="h-5 w-5" />
            </span>
            <div>
              <p className="text-xs uppercase tracking-[0.4em] text-slate-400">Dashboard</p>
              <p className="text-base font-semibold text-slate-900">Picky Admin</p>
            </div>
          </div>

          {user ? (
            <div className="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-sm text-slate-600">
              <p className="text-xs uppercase tracking-[0.3em] text-slate-400">Active user</p>
              <p className="pt-1 text-base font-medium text-slate-900">{user.display_name}</p>
              <p className="text-xs text-slate-500">{user.email}</p>
              <p className="mt-3 inline-flex rounded-full border border-slate-200 px-3 py-1 text-[11px] uppercase tracking-[0.3em] text-slate-500">
                {isVendor ? "Vendor" : isAdmin ? "Administrator" : "Member"}
              </p>
            </div>
          ) : (
            loadingState
          )}
        </div>

        {isVendor && renderLinks(vendorLinks, "Workspace")}
        {isAdmin && renderLinks(adminLinks, "Navigation")}
        {isUser && renderLinks(userLinks, "Navigation")}

        <div className="rounded-2xl border border-white bg-white px-4 py-4 text-center text-[11px] uppercase tracking-[0.3em] text-slate-400">
          © {new Date().getFullYear()} Kibs Platform
        </div>
      </div>
    </aside>
  );
};

export default Sidebar;
