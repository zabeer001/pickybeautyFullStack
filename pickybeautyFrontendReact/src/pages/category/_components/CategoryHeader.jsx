import React from "react";
import { motion } from "framer-motion";

export default function CategoryHeader() {
  return (
    <motion.div
      initial={{ opacity: 0, y: -20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6 }}
      className="mt-10 mb-10"
    >
      <h1 className="text-3xl md:text-4xl font-serif text-gray-800 mb-2">
        Pick my Beauty - Pick my Budget
      </h1>
      <p className="text-gray-600 italic font-serif">
        Hier gibst du den Preis vor!
      </p>
    </motion.div>
  );
}
