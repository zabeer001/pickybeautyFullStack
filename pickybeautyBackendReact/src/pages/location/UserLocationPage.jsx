import React, { useState } from "react";
import { LocateFixed, MapPinned, RefreshCcw, Save } from "lucide-react";
import { backendUrl } from "../../../env";

const apiBase = `${backendUrl}/wp-json/kibsterlp-admin/v1`;

const emptyLocation = {
  lat: "",
  lon: "",
  area: "Current browser position",
  city: "Waiting for permission",
  fullAddress: "",
  postcode: "N/A",
};

function mapReverseGeocodePayload(payload) {
  const address = payload?.address || {};

  return {
    area:
      address.suburb ||
      address.neighbourhood ||
      address.city_district ||
      address.quarter ||
      address.borough ||
      address.county ||
      "--",
    city:
      address.city ||
      address.town ||
      address.village ||
      address.municipality ||
      address.state ||
      "--",
    fullAddress: payload?.display_name || "",
    postcode: address.postcode || "N/A",
  };
}

export default function UserLocationPage() {
  const [radius, setRadius] = useState("10");
  const [useCurrentLocation, setUseCurrentLocation] = useState(true);
  const [locationData, setLocationData] = useState(emptyLocation);
  const [resolving, setResolving] = useState(false);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [message, setMessage] = useState("");

  const token = localStorage.getItem("jwt_token");

  const authHeaders = {
    "Content-Type": "application/json",
    Authorization: `Bearer ${token}`,
  };

  const applyBrowserLocation = (coords) => {
    const latitude = Number(coords.latitude).toFixed(6);
    const longitude = Number(coords.longitude).toFixed(6);

    setLocationData({
      lat: latitude,
      lon: longitude,
      area: "Browser geolocation",
      city: "Permission granted",
      fullAddress: `Latitude ${latitude}, Longitude ${longitude}`,
      postcode: "N/A",
    });
  };

  const enrichLocationWithAddress = async (latitude, longitude) => {
    try {
      setMessage("Location allowed. Looking up your full address...");

      const response = await fetch(
        `${apiBase}/geo/reverse?lat=${latitude}&lon=${longitude}`,
        {
          headers: authHeaders,
        }
      );

      if (!response.ok) {
        throw new Error(`Reverse geocoding failed (${response.status})`);
      }

      const payload = await response.json();
      const mapped = mapReverseGeocodePayload(payload);

      setLocationData((current) => ({
        ...current,
        area: mapped.area,
        city: mapped.city,
        fullAddress:
          mapped.fullAddress || current.fullAddress,
        postcode: mapped.postcode,
      }));

      setMessage("Browser location received and full address loaded.");
    } catch (reverseError) {
      console.error("Reverse geocoding failed:", reverseError);
      setMessage(
        "Coordinates received, but the full address could not be loaded."
      );
    }
  };

  const resolveCurrentLocation = async () => {
    if (!navigator.geolocation) {
      setError("Geolocation is not supported in this browser.");
      return;
    }

    if (!window.isSecureContext) {
      setError("Location access needs a secure page. Use localhost or HTTPS.");
      return;
    }

    setResolving(true);
    setError("");
    setMessage("Your browser should now ask to allow location access.");

    if (navigator.permissions?.query) {
      try {
        const permission = await navigator.permissions.query({
          name: "geolocation",
        });

        if (permission.state === "denied") {
          setResolving(false);
          setError(
            "Location access is blocked in your browser. Allow location for this site and try again."
          );
          return;
        }

        if (permission.state === "granted") {
          setMessage("Location permission already allowed. Reading coordinates now.");
        }
      } catch (permissionError) {
        console.error("Permission query failed:", permissionError);
      }
    }

    const fallbackTimer = window.setTimeout(() => {
      setResolving(false);
      setError(
        "No location prompt appeared. Check the browser address bar and allow location for this page."
      );
    }, 10000);

    navigator.geolocation.getCurrentPosition(
      async (position) => {
        window.clearTimeout(fallbackTimer);
        const { latitude, longitude } = position.coords;
        applyBrowserLocation({ latitude, longitude });
        await enrichLocationWithAddress(latitude, longitude);
        setResolving(false);
      },
      (geoError) => {
        window.clearTimeout(fallbackTimer);
        console.error("Geolocation failed:", geoError);
        setResolving(false);
        setError(
          geoError?.code === 1
            ? "Browser location was denied. Allow location access and try again."
            : "Browser location could not be read. Check site permissions and try again."
        );
      },
      {
        enableHighAccuracy: true,
        timeout: 12000,
        maximumAge: 0,
      }
    );
  };

  const handleSave = async () => {
    if (useCurrentLocation && (!locationData.lat || !locationData.lon)) {
      setError("Get your current location before saving.");
      return;
    }

    if (!radius || Number(radius) <= 0) {
      setError("Radius must be greater than 0.");
      return;
    }

    setSaving(true);
    setError("");
    setMessage("");

    try {
      const response = await fetch(`${apiBase}/user/location`, {
        method: "POST",
        headers: authHeaders,
        body: JSON.stringify({
          x: locationData.lat ? Number(locationData.lat) : null,
          y: locationData.lon ? Number(locationData.lon) : null,
          radius: Number(radius),
          full_address: locationData.fullAddress,
          status: useCurrentLocation ? "active" : "inactive",
        }),
      });

      const result = await response.json();

      if (!response.ok || result?.status !== true) {
        throw new Error(result?.message || "Failed to save location.");
      }

      setMessage(result?.message || "Location saved successfully.");
    } catch (saveError) {
      console.error("Failed to save location:", saveError);
      setError(saveError.message || "Could not save your location.");
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="space-y-6">
      <section className="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60">
        <div className="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.35em] text-amber-500">
              Location Profile
            </p>
            <h1 className="mt-3 text-3xl font-semibold text-slate-900">
              Manage Your Exact Location
            </h1>
            <p className="mt-2 max-w-2xl text-sm leading-7 text-slate-500">
              Click "Get My Location" and your browser will ask for location
              access. Once allowed, your current coordinates will appear here.
            </p>
          </div>

          <div className="flex flex-col gap-3 sm:flex-row">
            <label className="flex min-h-12 cursor-pointer items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-5 text-sm font-semibold text-slate-700">
              <input
                type="checkbox"
                checked={useCurrentLocation}
                onChange={(event) => setUseCurrentLocation(event.target.checked)}
                className="h-4 w-4"
              />
              Use current location
            </label>

            <button
              type="button"
              onClick={resolveCurrentLocation}
              disabled={resolving}
              className="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-slate-900 px-5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
            >
              <LocateFixed className="h-4 w-4" />
              {resolving ? "Fetching..." : "Get My Location"}
            </button>
          </div>
        </div>
      </section>

      {(error || message) && (
        <div
          className={`rounded-2xl border px-4 py-3 text-sm ${
            error
              ? "border-red-200 bg-red-50 text-red-700"
              : "border-emerald-200 bg-emerald-50 text-emerald-700"
          }`}
        >
          {error || message}
        </div>
      )}

      <div className="grid gap-6 xl:grid-cols-2">
        <section className="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-lg shadow-slate-200/50">
          <div className="mb-5 flex items-center gap-3">
            <span className="rounded-2xl bg-slate-100 p-3 text-slate-700">
                <MapPinned className="h-5 w-5" />
              </span>
              <div>
                <p className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">
                  Coordinates
                </p>
                <p className="text-sm text-slate-500">Current browser location</p>
              </div>
            </div>

          <div className="space-y-4">
            <div className="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
              <span className="text-sm text-slate-500">Latitude</span>
              <span className="font-semibold text-slate-900">
                {locationData.lat || "--"}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-sm text-slate-500">Longitude</span>
              <span className="font-semibold text-slate-900">
                {locationData.lon || "--"}
              </span>
            </div>
          </div>
        </section>

        <section className="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-lg shadow-slate-200/50">
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">
            Radius
          </p>
          <p className="mt-2 text-sm text-slate-500">
            Define how far your service area should cover.
          </p>

          <div className="mt-5">
            <label className="mb-2 block text-sm font-medium text-slate-600">
              Set radius (KM)
            </label>
            <input
              type="number"
              min="1"
              value={radius}
              onChange={(event) =>
                setRadius(event.target.value.replace(/[^\d]/g, ""))
              }
              className="w-full rounded-2xl border border-slate-300 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-900"
            />
          </div>
        </section>

        <section className="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-lg shadow-slate-200/50">
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">
            Area Details
          </p>

          <div className="mt-5 space-y-4">
            <div className="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
              <span className="text-sm text-slate-500">Source</span>
              <span className="font-semibold text-slate-900">
                {locationData.area || "--"}
              </span>
            </div>
            <div className="flex items-center justify-between border-b border-dashed border-slate-200 pb-4">
              <span className="text-sm text-slate-500">Status</span>
              <span className="font-semibold text-slate-900">
                {locationData.city || "--"}
              </span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-sm text-slate-500">Postcode</span>
              <span className="font-semibold text-slate-900">
                {locationData.postcode || "--"}
              </span>
            </div>
          </div>
        </section>

        <section className="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-lg shadow-slate-200/50">
          <p className="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">
            Full Address
          </p>
          <p className="mt-5 min-h-24 text-sm leading-7 text-slate-600">
            {locationData.fullAddress ||
              "No browser coordinates captured yet. Click Get My Location and allow the browser prompt."}
          </p>
        </section>
      </div>

      <div className="flex flex-col gap-3 sm:flex-row sm:justify-end">
        <button
          type="button"
          onClick={resolveCurrentLocation}
          disabled={resolving}
          className="inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
        >
          <RefreshCcw className="h-4 w-4" />
          {resolving ? "Refreshing..." : "Refresh Location"}
        </button>

        <button
          type="button"
          onClick={handleSave}
          disabled={saving}
          className="inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-slate-900 px-5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
        >
          <Save className="h-4 w-4" />
          {saving ? "Saving..." : "Save Location"}
        </button>
      </div>
    </div>
  );
}
