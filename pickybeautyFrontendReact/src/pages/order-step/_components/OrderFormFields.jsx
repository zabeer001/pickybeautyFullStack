import React, { useState } from "react";

export default function OrderFormFields({
  order,
  setOrderField,
  onAddressChange,
}) {
  const [isLocating, setIsLocating] = useState(false);
  const [locationMessage, setLocationMessage] = useState("");

  const formatDetectedAddress = (address = {}) => {
    const streetLine = [];
    const locationLine = [];

    const road =
      address.road ||
      address.pedestrian ||
      address.footway ||
      address.path;
    const houseNumber = address.house_number;
    const building = address.building || address.house || address.amenity;
    const suburb =
      address.suburb ||
      address.neighbourhood ||
      address.residential ||
      address.quarter;
    const city =
      address.city ||
      address.town ||
      address.village ||
      address.municipality ||
      address.county;

    if (building) {
      streetLine.push(building);
    }

    if (road && houseNumber) {
      streetLine.push(`${houseNumber} ${road}`);
    } else if (road) {
      streetLine.push(road);
    } else if (houseNumber) {
      streetLine.push(`House ${houseNumber}`);
    }

    [suburb, city, address.state, address.postcode, address.country]
      .filter(Boolean)
      .forEach((part) => locationLine.push(part));

    return [...streetLine, ...locationLine].join(", ").trim();
  };

  const handleAddressChange = (event) => {
    if (onAddressChange) {
      onAddressChange(event);
      return;
    }

    setOrderField("address", event.target.value);
  };

  const handleAutoFillAddress = async () => {
    let detectedCoords = null;

    if (!navigator.geolocation) {
      setLocationMessage("Dein Browser unterstuetzt keine Standortfreigabe.");
      return;
    }

    setIsLocating(true);
    setLocationMessage("");

    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0,
        });
      });

      const { latitude, longitude } = position.coords;
      detectedCoords = { latitude, longitude };

      setOrderField("x", latitude);
      setOrderField("y", longitude);

      const response = await fetch(
        `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(
          latitude
        )}&lon=${encodeURIComponent(longitude)}&zoom=18&addressdetails=1`,
        {
          headers: {
            Accept: "application/json",
          },
        }
      );

      if (!response.ok) {
        throw new Error("reverse-geocode-failed");
      }

      const data = await response.json();
      const structuredAddress = formatDetectedAddress(data.address);
      const fullAddress = structuredAddress || data.display_name?.trim();

      if (fullAddress) {
        setOrderField("address", fullAddress);
        setLocationMessage("");
        return;
      }

      const fallbackLocation = `Standort (${latitude.toFixed(5)}, ${longitude.toFixed(5)})`;
      setOrderField("address", fallbackLocation);
      setLocationMessage("Exakte Adresse nicht gefunden. Koordinaten wurden eingefuegt.");
    } catch (error) {
      if (error?.code === 1) {
        setLocationMessage("Standortfreigabe wurde verweigert.");
      } else if (error?.code === 2) {
        setLocationMessage("Standort konnte nicht ermittelt werden.");
      } else if (error?.code === 3) {
        setLocationMessage("Standortabfrage hat zu lange gedauert.");
      } else {
        if (detectedCoords) {
          const fallbackLocation = `Location (${detectedCoords.latitude.toFixed(
            5
          )}, ${detectedCoords.longitude.toFixed(5)})`;
          setOrderField("address", fallbackLocation);
          setLocationMessage(
            "Volle Adresse nicht gefunden. Koordinaten wurden stattdessen eingefuegt."
          );
        } else {
          setLocationMessage("Adresse konnte nicht automatisch ausgefuellt werden.");
        }
      }
    } finally {
      setIsLocating(false);
    }
  };

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
        <label className="text-sm font-medium text-gray-600">
          E-Mail-Adresse
        </label>
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

      {/* ✅ Simple Address Textarea */}
      <textarea
        placeholder="Adresse"
        value={order.address}
        onChange={handleAddressChange}
        rows={4}
        className="w-full border border-gray-300 rounded-xl p-3 resize-none focus:outline-none focus:ring-2 focus:ring-pink-400"
      />

      <div className="w-full flex flex-col items-start gap-2">
        <button
          type="button"
          onClick={handleAutoFillAddress}
          disabled={isLocating}
          className="rounded-xl border border-pink-300 px-4 py-2 text-sm font-medium text-pink-600 transition hover:bg-pink-50 disabled:cursor-not-allowed disabled:opacity-60"
        >
          {isLocating ? "Standort wird geladen..." : "Adresse automatisch ausfuellen"}
        </button>
        {locationMessage ? (
          <p className="text-xs text-gray-500">{locationMessage}</p>
        ) : null}
      </div>
    </>
  );
}
