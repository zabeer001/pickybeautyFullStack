import React, { useEffect, useState } from 'react';
import { motion } from "framer-motion";
import { useNavigate } from 'react-router-dom';
import { useCategoryStore } from '../stores/root/useHomeStore';
import { WP_BACKEND } from '../../env';

// ✅ WordPress backend base URL


const Category = () => {
  const navigate = useNavigate();
  const { setSelectedCategory } = useCategoryStore();

  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  // ✅ Fetch categories from WordPress API
  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const res = await fetch(`${WP_BACKEND}/wp-json/kibsterlp-admin/v1/categories`);
        if (!res.ok) throw new Error(`Failed to fetch categories: ${res.status}`);
        const data = await res.json();
        console.log(data.data);
        
        setCategories(data.data);
      } catch (error) {
        console.error("Error fetching categories:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchCategories();
  }, []);

  // ✅ Store only the category ID in Zustand
  const handleSelect = (cat) => {
    setSelectedCategory(cat.id); // ← store ID only
    navigate("location");
  };

  return (
    <div className='h-[600px] flex flex-col justify-center items-center'>
      {/* Header */}
      <motion.div
        initial={{ opacity: 0, y: -20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.6 }}
        className="mb-10"
      >
        <h1 className="text-3xl md:text-4xl font-serif text-gray-800 mb-2">
          Pick my Beauty - Pick my Budget
        </h1>
        <p className="text-gray-600 italic font-serif">
          Hier gibst du den Preis vor!
        </p>
      </motion.div>

      {/* Category Buttons */}
      {loading ? (
        <p className="text-gray-500 font-serif italic">Lade Kategorien...</p>
      ) : (
        <div className="grid grid-cols-1 gap-5 w-full max-w-xs md:max-w-md">
          {categories.length > 0 ? (
            categories.map((cat) => (
              <motion.button
                key={cat.id}
                whileHover={{ scale: 1.05 }}
                onClick={() => handleSelect(cat)}
                className="w-full bg-gray-100 hover:!bg-[#cc3366] text-gray-700 hover:text-white font-serif text-xl rounded-2xl py-4 shadow-md hover:shadow-lg transition-all duration-300"
              >
                {cat.title}
              </motion.button>
            ))
          ) : (
            <p className="text-gray-500 italic">Keine Kategorien gefunden</p>
          )}
        </div>
      )}
    </div>
  );
};

export default Category;
