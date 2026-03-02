import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import StepActions from "../../../components/StepActions";
import { useCategoryStore } from "../../../stores/root/useHomeStore";
import { WP_BACKEND } from "../../../../env";
import CategoryHeader from "./CategoryHeader";
import CategoryList from "./CategoryList";

// ✅ WordPress backend base URL

const Category = () => {
  const navigate = useNavigate();
  const { category, setSelectedCategory } = useCategoryStore();
  const normalizedCategoryId =
    typeof category === "object" && category !== null ? category.id ?? null : category;

  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedCategoryId, setSelectedCategoryId] = useState(normalizedCategoryId);

  useEffect(() => {
    if (normalizedCategoryId !== category) {
      setSelectedCategory(normalizedCategoryId);
    }
  }, [category, normalizedCategoryId, setSelectedCategory]);

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
    setSelectedCategoryId(cat.id);
    setSelectedCategory(cat.id);
  };

  const handleNext = () => {
    if (!selectedCategoryId) {
      return;
    }

    navigate("/location");
  };

  return (
    <div className="h-[700px] flex flex-col justify-start items-center mt-[20px] px-4">
      <CategoryHeader />
      <CategoryList
        categories={categories}
        loading={loading}
        onSelect={handleSelect}
        selectedCategoryId={selectedCategoryId}
      />
      <StepActions
        containerClassName="mt-10 flex w-full max-w-xs md:max-w-md items-center justify-between gap-4"
        nextDisabled={!selectedCategoryId}
        onBack={() => navigate("/")}
        onNext={handleNext}
      />
    </div>
  );
};

export default Category;
