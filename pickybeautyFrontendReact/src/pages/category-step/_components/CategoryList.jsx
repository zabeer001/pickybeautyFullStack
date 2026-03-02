import React from "react";
import { motion } from "framer-motion";

function CategoryButton({ category, isSelected, onSelect }) {
  return (
    <motion.button
      whileHover={{ scale: 1.05 }}
      onClick={() => onSelect(category)}
      className={`w-full font-serif text-xl rounded-2xl py-4 shadow-md transition-all duration-300 ${
        isSelected
          ? "bg-[#d2346d] text-white shadow-lg"
          : "bg-gray-100 text-gray-700 hover:!bg-[#cc3366] hover:text-white hover:shadow-lg"
      }`}
    >
      {category.title}
    </motion.button>
  );
}

export default function CategoryList({
  categories,
  loading,
  onSelect,
  selectedCategoryId,
}) {
  if (loading) {
    return <p className="text-gray-500 font-serif italic">Lade Kategorien...</p>;
  }

  return (
    <div className="grid grid-cols-1 gap-5 w-full max-w-xs md:max-w-md">
      {categories.length > 0 ? (
        categories.map((category) => (
          <CategoryButton
            key={category.id}
            category={category}
            isSelected={selectedCategoryId === category.id}
            onSelect={onSelect}
          />
        ))
      ) : (
        <p className="text-gray-500 italic">Keine Kategorien gefunden</p>
      )}
    </div>
  );
}
