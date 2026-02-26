import { create } from "zustand";
import { persist } from "zustand/middleware";

// ✅ Category store
export const useCategoryStore = create(
  persist(
    (set) => ({
      category: null,
      setSelectedCategory: (category) => set({ category }),
    }),
    {
      name: "selectedCategory-storage", // 🔹 key for localStorage
      getStorage: () => localStorage,   // optional — default is localStorage
    }
  )
);

// ✅ Zipcode store
export const useZipcodeStore = create(
  persist(
    (set) => ({
      zipcode: null,
      setSelectedZipcode: (zipcode) => set({ zipcode }), // ✅ fixed typo here
    }),
    {
      name: "selectedZipcode-storage", // 🔹 key for localStorage
      getStorage: () => localStorage,
    }
  )
);

// ✅ Budget store
export const useBudgetStore = create(
  persist(
    (set) => ({
      budget: null,
      setSelectedBudget: (budget) => set({ budget }),
    }),
    {
      name: "selectedBudget-storage",
      getStorage: () => localStorage,
    }
  )
);

export const useOrderStore = create(
  persist(
    (set) => ({
      order: {
        name: "",
        email: "",
        phone: "",
        address: "",
      },
      setOrderField: (field, value) =>
        set((state) => ({
          order: {
            ...state.order,
            [field]: value,
          },
        })),
      resetOrder: () =>
        set({
          order: {
            name: "",
            email: "",
            phone: "",
            address: "",
          },
        }),
    }),
    {
      name: "order-storage", // localStorage key
      getStorage: () => localStorage,
    }
  )
);
