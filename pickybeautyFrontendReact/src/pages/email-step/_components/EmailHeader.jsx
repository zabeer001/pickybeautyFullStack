import React from "react";

export default function EmailHeader() {
  return (
    <>
      <h1 className="text-3xl mt-10 mb-3 text-gray-800 font-semibold">
        Deine E-Mail-Adresse
      </h1>
      <p className="text-gray-600 mb-6 max-w-xl">
        Damit wir dich erreichen können, brauchen wir noch eine gültige
        E-Mail-Adresse. Sie wird automatisch in die finale Bestellung übernommen.
      </p>
    </>
  );
}
