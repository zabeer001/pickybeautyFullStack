import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import germanZipcodes from "../../../data/germanZipcodes.json";
import { useZipcodeStore } from "../../../stores/root/useHomeStore";
import LocationHeader from "./LocationHeader";
import LocationSearch from "./LocationSearch";

function LocationStep() {
  const [location, setLocation] = useState("");
  const [suggestions, setSuggestions] = useState([]);
  const { setSelectedZipcode } = useZipcodeStore();
  const navigate = useNavigate();

  const handleChange = (event) => {
    const input = event.target.value.trim();
    setLocation(input);

    if (input.length > 0) {
      const filtered = germanZipcodes.filter((item) => {
        const zip = item.zipcode?.toString() || "";
        const place = item.place?.toLowerCase() || "";

        return zip.startsWith(input) || place.includes(input.toLowerCase());
      });

      setSuggestions(filtered.slice(0, 10));
    } else {
      setSuggestions([]);
    }
  };

  const handleSelect = (item) => {
    const fullLocation = `${item.zipcode} ${item.place}`;
    setLocation(fullLocation);
    setSuggestions([]);
    setSelectedZipcode(fullLocation);
  };

  const handleNext = () => {
    const valid = germanZipcodes.some(
      (item) =>
        `${item.zipcode} ${item.place}`.toLowerCase() === location.toLowerCase()
    );

    if (valid) {
      setSelectedZipcode(location);
      navigate("/budget", { state: { location } });
    } else {
      window.alert("Bitte wähle einen gültigen Standort aus der Liste!");
    }
  };

  return (
    <div className="h-[700px] flex flex-col justify-start items-center mt-[20px] px-4">
      <LocationHeader />
      <LocationSearch
        location={location}
        onChange={handleChange}
        onSelect={handleSelect}
        suggestions={suggestions}
      />
      <StepActions
        containerClassName="mt-10 flex w-full max-w-md items-center justify-between gap-6"
        onBack={() => navigate("/category")}
        onNext={handleNext}
      />
    </div>
  );
}

export default LocationStep;
