import React from "react";

export default function EmailInput({ error, onChange, value }) {
  return (
    <div className="w-full max-w-md flex flex-col gap-3">
      <input
        type="email"
        value={value}
        onChange={onChange}
        placeholder="name@beispiel.de"
        className="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-pink-400 text-center"
      />
      {error && <p className="text-red-500 text-sm">{error}</p>}
    </div>
  );
}
