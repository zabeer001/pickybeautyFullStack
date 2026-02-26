import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import germanZipcodes from "../data/germanZipcodes.json"; // path to your JSON
import { useZipcodeStore } from "../stores/root/useHomeStore";


function Location() {
  const [location, setLocation] = useState("");
  const [suggestions, setSuggestions] = useState([]);
  const { setSelectedZipcode } = useZipcodeStore(); // ✅ correct hook usage
  const navigate = useNavigate();

  // 🔍 Handle input and suggest matching zip/city pairs
  const handleChange = (e) => {
    const input = e.target.value.trim();
    setLocation(input);

    if (input.length > 0) {
      const filtered = germanZipcodes.filter((item) => {
        const zip = item.zipcode?.toString() || "";
        const place = item.place?.toLowerCase() || "";
        return (
          zip.startsWith(input) || place.includes(input.toLowerCase())
        );
      });
      setSuggestions(filtered.slice(0, 10)); // show top 10 results
    } else {
      setSuggestions([]);
    }
  };

  // ✅ Save selected location and update input
  const handleSelect = (item) => {
    const fullLocation = `${item.zipcode} ${item.place}`;
    setLocation(fullLocation);
    setSuggestions([]);
    setSelectedZipcode(fullLocation); // ✅ save to Zustand store
  };

  // 🚀 Move to next step only if valid
  const handleNext = () => {
    const valid = germanZipcodes.some(
      (item) =>
        `${item.zipcode} ${item.place}`.toLowerCase() ===
        location.toLowerCase()
    );

    if (valid) {
      setSelectedZipcode(location); // ✅ ensure store is updated even if typed manually
      navigate("/budget", { state: { location } });
    } else {
      alert("Bitte wähle einen gültigen Standort aus der Liste!");
    }
  };

  return (
    <div className="h-[600px] flex flex-col justify-top items-center mt-[120px]">
      <h1 className="text-3xl mb-6 text-gray-800 font-semibold">
        Wähle deinen Standort
      </h1>

      <div className="w-full max-w-md relative">
        <input
          type="text"
          value={location}
          onChange={handleChange}
          placeholder="PLZ oder Stadt eingeben..."
          className="w-full border border-gray-300 rounded-xl p-3 text-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
        />

        {suggestions.length > 0 && (
          <ul className="absolute bg-white border border-gray-200 rounded-xl mt-1 w-full max-h-56 overflow-y-auto shadow-md z-10">
            {suggestions.map((sug, index) => (
              <li
                key={index}
                onClick={() => handleSelect(sug)}
                className="p-3 cursor-pointer hover:bg-pink-100 transition-colors"
              >
                <span className="font-semibold">{sug.zipcode}</span> —{" "}
                <span>{sug.place}</span>
              </li>
            ))}
          </ul>
        )}
      </div>

      <button
        onClick={handleNext}
        className="mt-6 bg-[#cc3366] text-white text-xl px-6 py-3 rounded-2xl shadow-md transition-all duration-300 hover:!bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        Weiter →
      </button>
    </div>
  );
}

export default Location;
