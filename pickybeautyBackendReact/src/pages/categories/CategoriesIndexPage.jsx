import React, { useEffect, useState } from 'react';
import { backendUrl } from '../../../env';
import { Link } from 'react-router-dom';
import { Edit, Trash, Search } from 'lucide-react';
import { showErrorToast, showSuccessToast } from '../../utils/toast'; // ✅ Correct imports

function CategoriesIndexPage() {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [sortBy, setSortBy] = useState('latest'); // latest | oldest | title

  useEffect(() => {
    fetchCategories();
  }, []);

  // ✅ Fetch categories
  const fetchCategories = async () => {
    try {
      setLoading(true);
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/categories`);
      if (!response.ok) throw new Error(`HTTP Error ${response.status}`);
      const data = await response.json();
      setCategories(data.data || []);
    } catch (error) {
      console.error('Error fetching categories:', error);
      showErrorToast('Failed to fetch categories.');
    } finally {
      setLoading(false);
    }
  };

  // ✅ Delete handler
  const handleDelete = async (id) => {
    const confirmed = window.confirm('Are you sure you want to delete this category?');
    if (!confirmed) return;

    const token = localStorage.getItem('jwt_token');

    try {
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/categories/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });

      const result = await response.json();

      if (response.ok) {
        showSuccessToast('Category deleted successfully!');
        setCategories((prev) => prev.filter((cat) => cat.id !== id));
      } else {
        showErrorToast(result.message || 'Failed to delete category.');
      }
    } catch (error) {
      console.error('Error deleting category:', error);
      showErrorToast('Error deleting category.');
    }
  };

  // ✅ Filter & sort categories
  const filteredCategories = categories
    .filter(
      (cat) =>
        cat.title?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        cat.id?.toString().includes(searchTerm)
    )
    .sort((a, b) => {
      if (sortBy === 'title') return a.title.localeCompare(b.title);
      if (sortBy === 'oldest') return new Date(a.created_at) - new Date(b.created_at);
      return new Date(b.created_at) - new Date(a.created_at);
    });

  // ✅ Loading UI
  if (loading) {
    return (
      <div className="!flex !justify-center !items-center !min-h-screen !bg-gradient-to-br !from-blue-50 !to-indigo-50">
        <div className="!text-center">
          <div className="!animate-spin !rounded-full !h-12 !w-12 !border-b-2 !border-blue-600 !mx-auto !mb-4"></div>
          <p className="!text-gray-600 !text-lg !font-medium">Loading categories...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="!max-w-7xl !mx-auto !mt-4 !p-4 !text-slate-900">
      <div className="!mb-4 !flex !items-center !justify-between">
        <h1 className="!text-2xl !font-bold !text-slate-800">Categories</h1>
        <Link
          to="/admin/categories/create"
          className="!inline-flex !items-center !rounded-xl !bg-indigo-600 !px-4 !py-2 !text-sm !font-semibold !text-white !shadow-lg !shadow-indigo-500/40 !transition hover:!bg-indigo-700"
        >
          + Create Category
        </Link>
      </div>

      <div className="!overflow-hidden !rounded-3xl !border !border-white !bg-white !shadow-2xl !shadow-slate-200/80">
        <table className="!min-w-full !divide-y !divide-slate-100">
          <thead className="!bg-slate-50 !text-xs !uppercase !tracking-[0.2em] !text-slate-500">
            <tr>
              <th className="!px-6 !py-3 !text-left">ID</th>
              <th className="!px-6 !py-3 !text-left">Title</th>
              <th className="!px-6 !py-3 !text-left">Description</th>
              <th className="!px-6 !py-3 !text-left">Actions</th>
            </tr>
          </thead>
          <tbody className="!bg-white !divide-y !divide-slate-100 !text-sm">
            {filteredCategories.length === 0 ? (
              <tr>
                <td colSpan="4" className="!text-center !py-6 !text-slate-500">
                  No categories found.
                </td>
              </tr>
            ) : (
              filteredCategories.map((cat) => (
                <tr key={cat.id} className="!transition !hover:bg-slate-50">
                  <td className="!px-6 !py-4 !font-semibold !text-slate-600">#{cat.id}</td>
                  <td className="!px-6 !py-4 !text-base !font-semibold !text-slate-900">{cat.title}</td>
                  <td className="!px-6 !py-4 !text-slate-600">{cat.description}</td>
                  <td className="!px-6 !py-4">
                    <div className="!flex !items-center !gap-2">
                      <Link
                        to={`/admin/categories/edit/${cat.id}`}
                        title="Edit"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-emerald-300 !hover:text-emerald-600"
                      >
                        <Edit className="!h-4 !w-4" />
                      </Link>

                      <button
                        title="Delete"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-rose-300 !hover:text-rose-600"
                        onClick={() => handleDelete(cat.id)}
                      >
                        <Trash className="!h-4 !w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default CategoriesIndexPage;
