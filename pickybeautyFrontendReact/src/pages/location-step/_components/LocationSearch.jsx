import React from "react";

export default function LocationSearch({
  location,
  onChange,
  onSelect,
  suggestions,
}) {
  return (
    <div className="w-full max-w-md relative">
      <input
        type="text"
        value={location}
        onChange={onChange}
        placeholder="PLZ oder Stadt eingeben..."
        className="w-full border border-gray-300 rounded-xl p-3 text-lg focus:outline-none focus:ring-2 focus:ring-pink-400 bg-white"
      />

      {suggestions.length > 0 && (
        <ul className="absolute bg-white border border-gray-200 rounded-xl mt-1 w-full max-h-56 overflow-y-auto shadow-md z-10">
          {suggestions.map((suggestion, index) => (
            <li
              key={`${suggestion.zipcode}-${suggestion.place}-${index}`}
              onClick={() => onSelect(suggestion)}
              className="p-3 cursor-pointer hover:bg-pink-100 transition-colors"
            >
              <span className="font-semibold">{suggestion.zipcode}</span> -{" "}
              <span>{suggestion.place}</span>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
