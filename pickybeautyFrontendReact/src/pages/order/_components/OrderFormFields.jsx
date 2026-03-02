import React from "react";

export default function OrderFormFields({ order, setOrderField }) {
  return (
    <>
      <input
        type="text"
        placeholder="Vollständiger Name"
        value={order.name}
        onChange={(event) => setOrderField("name", event.target.value)}
        className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400"
      />

      <div className="w-full flex flex-col gap-2 text-left">
        <label className="text-sm font-medium text-gray-600">E-Mail-Adresse</label>
        <div className="flex-1 border border-gray-300 rounded-xl p-3 bg-white text-gray-700">
          {order.email || "Noch keine E-Mail angegeben"}
        </div>
        <p className="text-xs text-gray-500">
          Die E-Mail kannst du jederzeit vor der Bestellung erneut eingeben.
        </p>
      </div>

      <input
        type="tel"
        placeholder="Telefonnummer"
        value={order.phone}
        onChange={(event) => setOrderField("phone", event.target.value)}
        className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400"
      />

      <textarea
        placeholder="Adresse"
        rows="3"
        value={order.address}
        onChange={(event) => setOrderField("address", event.target.value)}
        className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 resize-none"
      />
    </>
  );
}
