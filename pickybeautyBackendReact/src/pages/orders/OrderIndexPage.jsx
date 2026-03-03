import React, { useEffect, useMemo, useState } from "react";
import { fetchOrders } from "../../api/orders/fetchOrders";
import { Eye, Edit, Trash, Search, Filter } from "lucide-react";
import { Link } from "react-router-dom";
import { backendUrl } from "../../../env";
import { showErrorToast, showSuccessToast } from "../../utils/toast";

function OrderIndexPage() {
  const [orders, setOrders] = useState([]);
  const [dataLoading, setDataLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState("");
  const [sharing_status, setSharing_status] = useState("all");
  const [payment_status, setPayment_status] = useState("all");
  const [category_id, setCategory_id] = useState("all");
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [categories, setCategories] = useState([]);
  const [categoryLoading, setCategoryLoading] = useState(false);

  const apiBase = `${backendUrl}/wp-json/kibsterlp-admin/v1`;

  useEffect(() => {
    const delay = setTimeout(() => {
      const loadOrders = async () => {
        try {
          setDataLoading(true);
          const token = localStorage.getItem("jwt_token");

          const data = await fetchOrders({
            page: currentPage,
            search: searchTerm.trim() === "" ? undefined : searchTerm.trim(),
            token,
            category_id,
            sharing_status,
            payment_status,
          });

          const { orders, totalPages } = data;

          const safeTotalPages = Number(totalPages);
          setTotalPages(safeTotalPages > 0 ? safeTotalPages : 1);
          setOrders(Array.isArray(orders) ? orders : []);
        } catch (error) {
          console.error("Error fetching orders:", error);
          setOrders([]);
          setTotalPages(1);
        } finally {
          setDataLoading(false);
        }
      };

      loadOrders();
    }, 400);

    return () => clearTimeout(delay);
  }, [currentPage, searchTerm, category_id, sharing_status, payment_status]);

  useEffect(() => {
    const fetchCategories = async () => {
      setCategoryLoading(true);
      try {
        const res = await fetch(`${apiBase}/categories`);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();

        if (data?.data && Array.isArray(data.data)) {
          setCategories(data.data);
        } else {
          setCategories([]);
        }
      } catch (error) {
        console.error("❌ Error fetching categories:", error);
        setCategories([]);
      } finally {
        setCategoryLoading(false);
      }
    };

    fetchCategories();
  }, [apiBase]);

  const formatCurrency = (amount) =>
    new Intl.NumberFormat("de-DE", { style: "currency", currency: "EUR" }).format(
      amount || 0
    );

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this order?")) return;

    const token = localStorage.getItem("jwt_token");

    try {
      const res = await fetch(`${apiBase}/orders/${id}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });

      if (!res.ok) throw new Error("Failed to delete order");

      setOrders((prev) => prev.filter((order) => order.id !== id));
      showSuccessToast("Order deleted successfully!");
    } catch (error) {
      console.error(error);
      showErrorToast("Error deleting order");
    }
  };

  const stats = useMemo(() => {
    const acceptedOrders = orders.filter(
      (order) =>
        order.vendor_id !== null &&
        order.vendor_id !== undefined &&
        order.vendor_id !== "" &&
        Number(order.vendor_id) !== 0
    );

    const pendingPayments = orders.filter(
      (order) => order.payment_status === "pending"
    );

    const totalBudget = orders.reduce((acc, order) => acc + Number(order?.budget || 0), 0);
    const totalPrice = orders.reduce((acc, order) => acc + Number(order?.price || 0), 0);

    return [
      { label: "Total Orders", value: orders.length, description: "Orders loaded on this page" },
      { label: "Assigned", value: acceptedOrders.length, description: "Orders with a vendor" },
      { label: "Pending Payment", value: pendingPayments.length, description: "Awaiting confirmation" },
      {
        label: "Budget vs Price",
        value: `${formatCurrency(totalBudget)} / ${formatCurrency(totalPrice)}`,
        description: "Aggregate of visible items",
      },
    ];
  }, [orders]);

  const activeFilters = useMemo(() => {
    const filters = [];
    if (searchTerm.trim()) filters.push(`Search: ${searchTerm.trim()}`);
    if (category_id !== "all")
      filters.push(
        `Category: ${
          categories.find((cat) => String(cat.id) === String(category_id))?.title || category_id
        }`
      );
    if (sharing_status !== "all") filters.push(`Status: ${sharing_status}`);
    if (payment_status !== "all") filters.push(`Payment: ${payment_status}`);
    return filters;
  }, [searchTerm, category_id, sharing_status, payment_status, categories]);

  const resetFilters = () => {
    setSearchTerm("");
    setCategory_id("all");
    setSharing_status("all");
    setPayment_status("all");
    setCurrentPage(1);
  };

  return (
    <div className="!space-y-6 !text-slate-900">
      <div className="!flex !items-center !justify-between">
        <h1 className="!text-2xl !font-bold !text-slate-800">Orders</h1>
      </div>

      <section className="!grid !w-full !grid-cols-4 !gap-4 !sm:grid-cols-2 !lg:grid-cols-4">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="!w-full !rounded-2xl !border !border-white !bg-white !px-5 !py-4 !text-slate-900 !shadow-lg !shadow-slate-200/80"
          >
            <p className="!text-xs !uppercase !tracking-[0.3em] !text-slate-400">{stat.label}</p>
            <p className="!pt-3 !text-2xl !font-semibold">{stat.value}</p>
            <p className="!text-xs !text-slate-500">{stat.description}</p>
          </div>
        ))}
      </section>

      {/* Filters */}
      <div className="!rounded-3xl !border !border-white !bg-white !p-5 !text-slate-900 !shadow-2xl !shadow-slate-200/70">
        <div className="!flex !w-full !flex-nowrap !items-center !justify-between !gap-3">
          <div className="!flex !flex-nowrap !items-center !gap-3">
            <div className="!flex !items-center !gap-2 !rounded-2xl !border !border-slate-200 !bg-slate-50 !px-2 !py-2 !w-[420px]">
              <Search className="!h-4 !w-4 !text-slate-400" />
              <input
                type="text"
                placeholder="Search orders..."
                value={searchTerm}
                onChange={(e) => {
                  setSearchTerm(e.target.value);
                  setCurrentPage(1);
                }}
                className="!w-full !bg-transparent !text-sm !text-slate-900 !placeholder:text-slate-400 !outline-none !border-0 !shadow-none !focus:shadow-none"
              />
            </div>
          </div>

          <div className="!flex !flex-nowrap !items-center !gap-3 !justify-end">
            <select
              value={category_id}
              onChange={(e) => {
                setCategory_id(e.target.value);
                setCurrentPage(1);
              }}
              className="!shrink-0 !rounded-2xl !border !border-slate-200 !bg-white !px-4 !py-3 !text-sm !text-slate-900 !outline-none !transition !focus:border-cyan-400/60"
            >
              <option value="all">All Categories</option>
              {categoryLoading ? (
                <option value="loading" disabled>
                  Loading...
                </option>
              ) : (
                categories.map((cat) => (
                  <option key={cat.id} value={cat.id} className="!bg-white !text-slate-900">
                    {cat.title}
                  </option>
                ))
              )}
            </select>

            <select
              value={sharing_status}
              onChange={(e) => {
                setSharing_status(e.target.value);
                setCurrentPage(1);
              }}
              className="!shrink-0 !rounded-2xl !border !border-slate-200 !bg-white !px-4 !py-3 !text-sm !text-slate-900 !outline-none !transition !focus:border-cyan-400/60"
            >
              <option value="all">Status</option>
              <option value="not accepted">Not Accepted</option>
              <option value="accepted">Accepted</option>
            </select>

            <select
              value={payment_status}
              onChange={(e) => {
                setPayment_status(e.target.value);
                setCurrentPage(1);
              }}
              className="!shrink-0 !rounded-2xl !border !border-slate-200 !bg-white !px-4 !py-3 !text-sm !text-slate-900 !outline-none !transition !focus:border-cyan-400/60"
            >
              <option value="all">Payment Status</option>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="cancel">Cancel</option>
            </select>

            <button
              onClick={resetFilters}
              className="!shrink-0 !inline-flex !items-center !gap-2 !rounded-2xl !border !border-slate-200 !bg-white !px-4 !py-3 !text-sm !text-slate-600 !transition !hover:border-slate-400 !hover:text-slate-900"
            >
              <Filter className="!h-4 !w-4" />
              Reset filters
            </button>
          </div>
        </div>

        {activeFilters.length > 0 && (
          <div className="!mt-4 !flex !flex-wrap !gap-2">
            {activeFilters.map((filter) => (
              <span
                key={filter}
                className="!inline-flex !items-center !rounded-full !border !border-slate-200 !bg-slate-50 !px-3 !py-1 !text-xs !font-medium !text-slate-600"
              >
                {filter}
              </span>
            ))}
          </div>
        )}
      </div>

      {/* Table + Pagination (fixed nesting/closing tags) */}
      <div className="!overflow-hidden !rounded-3xl !border !border-white !bg-white !shadow-2xl !shadow-slate-200/80">
        <div className="!w-full !overflow-x-auto">
          {dataLoading ? (
            <div className="!flex !flex-col !items-center !justify-center !gap-3 !px-6 !py-16 !text-slate-500">
              <div className="!h-10 !w-10 !animate-spin !rounded-full !border-2 !border-slate-200 !border-t-cyan-400" />
              Loading orders...
            </div>
          ) : orders.length === 0 ? (
            <div className="!px-6 !py-20 !text-center !text-slate-500">
              No orders match the selected filters.
            </div>
          ) : (
            <table className="!min-w-[1200px] !divide-y !divide-slate-100 !text-sm !text-slate-700">
              <thead className="!bg-slate-50 !text-xs !uppercase !tracking-[0.2em] !text-slate-500">
                <tr>
                  {["Order", "Vendor", "Category", "Email", "Phone", "Payment", "Budget & Price", "Location", "Actions"].map(
                    (h) => (
                      <th key={h} className="!px-6 !py-4 !text-left">
                        {h}
                      </th>
                    )
                  )}
                </tr>
              </thead>
              <tbody className="!divide-y !divide-slate-100">
                {orders.map((order, index) => (
                  <tr key={order.id || index} className="!transition !hover:bg-slate-50">
                    <td className="!px-6 !py-4">
                      <div className="!flex !items-center !gap-4">
                        <div className="!flex !h-10 !w-10 !items-center !justify-center !rounded-2xl !border !border-cyan-200 !bg-cyan-50 !text-sm !font-semibold !text-cyan-700">
                          # {order.id}
                        </div>
                       
                      </div>
                    </td>

                    <td className="!px-6 !py-4">
                      {order.vendor_id !== null &&
                      order.vendor_id !== undefined &&
                      order.vendor_id !== "" &&
                      Number(order.vendor_id) !== 0 ? (
                        <span className="!inline-flex !rounded-full !border !border-emerald-200 !bg-emerald-50 !px-3 !py-1 !text-xs !font-medium !text-emerald-700">
                          #{order.vendor_id}
                        </span>
                      ) : (
                        <span className="!inline-flex !rounded-full !border !border-rose-200 !bg-rose-50 !px-3 !py-1 !text-xs !font-medium !text-rose-700">
                          Not accepted
                        </span>
                      )}
                    </td>

                    <td className="!px-6 !py-4 !text-slate-600">{order.category?.title || "—"}</td>

                    <td className="!px-6 !py-4">
                      <div className="!max-w-[260px] !text-slate-600 !break-words !whitespace-normal">
                        {order.email || "—"}
                      </div>
                    </td>

                    <td className="!px-6 !py-4 !text-slate-600">{order.phone || "—"}</td>

                    <td className="!px-6 !py-4">
                      <span
                        className={[
                          "inline-flex rounded-full px-3 py-1 text-xs font-medium capitalize",
                          order.payment_status === "pending"
                            ? "border border-amber-200 bg-amber-50 text-amber-700"
                            : order.payment_status === "paid"
                            ? "border border-emerald-200 bg-emerald-50 text-emerald-700"
                            : "border border-slate-200 bg-white text-slate-600",
                        ].join(" ")}
                      >
                        {order.payment_status}
                      </span>
                    </td>

                    <td className="!px-6 !py-4">
                      <p className="!text-xs !uppercase !tracking-[0.2em] !text-slate-400">Budget</p>
                      <p className="!font-semibold !text-slate-800">{formatCurrency(order.budget)}</p>
                      <p className="!mt-2 !text-xs !uppercase !tracking-[0.2em] !text-slate-400">Price</p>
                      <p className="!font-semibold !text-slate-900">{formatCurrency(order.price)}</p>
                    </td>

                    <td className="!px-6 !py-4">
                      <div className="!space-y-1 !text-slate-500">
                        <p>{order.zip_code || "N/A"}</p>
                        <p>
                          <span className="!font-medium !text-slate-700">Latitude:</span>{" "}
                          {order.x ?? "—"}
                        </p>
                        <p>
                          <span className="!font-medium !text-slate-700">Longitude:</span>{" "}
                          {order.y ?? "—"}
                        </p>
                      </div>
                    </td>

                    <td className="!px-6 !py-4">
                      <div className="!flex !items-center !gap-2">
                        <Link
                          to={`/admin/orders/${order.order_unique_id}`}
                          className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-cyan-300 !hover:text-cyan-600"
                        >
                          <Eye className="!h-4 !w-4" />
                        </Link>

                        <Link
                          to={`/admin/orders/edit/${order.order_unique_id}`}
                          className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-emerald-300 !hover:text-emerald-600"
                        >
                          <Edit className="!h-4 !w-4" />
                        </Link>

                        <button
                          className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-rose-300 !hover:text-rose-600"
                          onClick={() => handleDelete(order.id)}
                        >
                          <Trash className="!h-4 !w-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>

        <div className="!flex !flex-col !gap-3 !rounded-2xl !border !border-white !bg-white !px-6 !py-5 !text-sm !items-end !justify-end !text-slate-600 !shadow-xl !shadow-slate-200/70 !md:flex-row !md:items-center !md:justify-between">
          <div className="!flex !items-center !gap-2">
            <button
              disabled={currentPage <= 1}
              onClick={() => setCurrentPage((p) => Math.max(p - 1, 1))}
              className="!rounded-2xl !border !border-slate-200 !px-4 !py-2 !transition !disabled:opacity-40 !hover:border-slate-400 !hover:text-slate-900"
            >
              Previous
            </button>

            <span>
              Page {currentPage} of {totalPages}
            </span>

            <button
              disabled={currentPage >= totalPages}
              onClick={() => setCurrentPage((p) => Math.min(p + 1, totalPages))}
              className="!rounded-2xl !border !border-slate-200 !px-4 !py-2 !transition !disabled:opacity-40 !hover:border-slate-400 !hover:text-slate-900"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default OrderIndexPage;
