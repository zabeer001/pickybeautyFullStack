import React, { useEffect, useState } from "react";
import {
  useBudgetStore,
  useCategoryStore,
  useZipcodeStore,
  useOrderStore,
} from "../stores/root/useHomeStore";
import { WP_BACKEND } from "../../env.js";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";

function Order() {
  // ✅ Get Zustand values
  const { category } = useCategoryStore();
  const { zipcode } = useZipcodeStore();
  const { budget } = useBudgetStore();
  const { order, setOrderField, resetOrder } = useOrderStore();
  const navigate = useNavigate(); // must be inside your component
  const { setSelectedBudget } = useBudgetStore(); // ✅ Zustand setter

  const [discountMessage, setDiscountMessage] = useState("");
  const [discountBudget, setDiscountBudget] = useState(budget);




  // ✅ Log on mount (optional)
  useEffect(() => {
    console.log("🧾 Zustand Data:");
    console.log("Category:", category);
    console.log("Zipcode:", zipcode);
    console.log("Budget:", budget);
    console.log("Order Info:", order);
  }, [category, zipcode, budget, order]);


  useEffect(() => {
    const fetchDiscount = async () => {
      // Only call if we have what the API needs
      if (!order.email || !budget) {
        console.log("⏭️ Skipping discount API call (missing email or budget)", {
          email: order.email,
          budget,
        });
        return;
      }

      try {
        const res = await fetch(
          `${WP_BACKEND}/wp-json/kibsterlp-admin/v1/order-discount`,
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              email: order.email,
              budget: budget,
            }),
          }
        );

        const data = await res.json();
        console.log("✅ Discount API response:", data);

        const discounted_budget = data?.data?.discounted_budget ?? budget;
        const discount_percentage = data?.data?.discount_percentage ?? 0;

        console.log("discount_percentage", discount_percentage);
        console.log("✅ Discount budget", discounted_budget);

      
        setDiscountBudget(discounted_budget);

        if (discount_percentage > 0 && discounted_budget !== budget) {
          console.log("yes discount");
          setDiscountMessage(`you got ${discount_percentage}% discount`);
        } else {
          console.log("no discount");
          setDiscountMessage("");
        }

      } catch (err) {
        console.error("❌ Discount API error:", err);
      }
    };

    fetchDiscount();
  }, []); // runs once on first render

  // ✅ Handle submit
  const handleSubmit = (e) => {
    e.preventDefault();


    const finalOrder = {
      category_id: category,
      budget: discountBudget,
      shipping: {
        name: order.name,
        email: order.email,
        phone: order.phone,
        zip_code: zipcode,
        address: order.address,
      },
    };

    console.log("🧾 FINAL ORDER DATA:", finalOrder);

    fetch(`${WP_BACKEND}/wp-json/kibsterlp-admin/v1/orders`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(finalOrder),
    })
      .then((res) => res.json())
      .then((data) => {
        console.log("✅ Order saved:", data);

        if (data.status === true) {
          resetOrder();

          toast.success(data.message || "Order placed successfully!", {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            onClose: () => navigate("/"), // 🚀 redirect after toast closes
          });
        } else {
          toast.error(data.message || "Something went wrong!", {
            position: "top-right",
            autoClose: 3000,
          });
        }
      })
      .catch((err) => {
        console.error("❌ Error saving order:", err);
        toast.error("Server error. Please try again later.", {
          position: "top-right",
          autoClose: 3000,
        });
      });
  };


  return (

    <div className="h-[600px] flex flex-col justify-top items-center mt-[120px]">
      <h1 className="text-3xl mb-6 text-gray-800">Kundendetails eingeben</h1>

      {/* ✅ Display Zustand data */}
      <div className="bg-gray-100 p-4 rounded-xl mb-6 shadow-md text-gray-700 w-full max-w-md">
        <p><strong>Kategorie:</strong> {category || "—"}</p>
        <p><strong>Standort:</strong> {zipcode || "—"}</p>
        <strong>Budget:</strong> {budget ? `${discountBudget} €` : "—"} {discountMessage}
      </div>

      <form
        onSubmit={handleSubmit}
        className="w-full max-w-md flex flex-col space-y-4"
      >
        <input
          type="text"
          placeholder="Vollständiger Name"
          value={order.name}
          onChange={(e) => setOrderField("name", e.target.value)}
          className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400"
        />

        <div className="w-full flex flex-col gap-2 text-left">
          <label className="text-sm font-medium text-gray-600">E-Mail-Adresse</label>
          <div className="flex items-center justify-between gap-3">
            <div className="flex-1 border border-gray-300 rounded-xl p-3 bg-white text-gray-700">
              {order.email || "Noch keine E-Mail angegeben"}
            </div>

          </div>
          <p className="text-xs text-gray-500">
            Die E-Mail kannst du jederzeit vor der Bestellung erneut eingeben.
          </p>
        </div>

        <input
          type="tel"
          placeholder="Telefonnummer"
          value={order.phone}
          onChange={(e) => setOrderField("phone", e.target.value)}
          className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400"
        />

        <textarea
          placeholder="Adresse"
          rows="3"
          value={order.address}
          onChange={(e) => setOrderField("address", e.target.value)}
          className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
        ></textarea>

        <button
          type="submit"
          className="mt-6 bg-[#cc3366] text-white text-xl px-6 py-3 rounded-2xl shadow-md transition-all duration-300 hover:!bg-red-700 hover:!border-red-700"
        >
          Bestellung bestätigen
        </button>
      </form>
    </div>
  );
}

export default Order;
