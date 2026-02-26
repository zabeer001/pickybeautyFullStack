import React, { useEffect, useState } from 'react';
import { backendUrl } from '../../../env'; // Assuming env is at the same relative path
import { Link } from 'react-router-dom';
import { Edit, Trash } from 'lucide-react'; // Search is not needed for now, but kept in for consistency
import { showErrorToast, showSuccessToast } from '../../utils/toast'; // Assuming toast utilities are available

// Define the shape of a loyalty entry for better clarity
// Note: created_at and updated_at might be '0000-00-00 00:00:00', so we'll handle that.
/**
 * @typedef {object} LoyaltyEntry
 * @property {number} id
 * @property {number} min_order
 * @property {number} max_order
 * @property {number} order_complete_percentage
 * @property {number} discount
 * @property {string} status
 * @property {string} created_at
 * @property {string} updated_at
 */

function LoyaltyIndexPage() {
  /** @type {[LoyaltyEntry[], React.Dispatch<React.SetStateAction<LoyaltyEntry[]>>]} */
  const [loyaltyEntries, setLoyaltyEntries] = useState([]);
  const [loading, setLoading] = useState(true);
  // Placeholder state for future search/sort functionality
  const [searchTerm, setSearchTerm] = useState('');
  const [sortBy, setSortBy] = useState('latest');

  useEffect(() => {
    fetchLoyaltyEntries();
  }, []);

  // ✅ Fetch loyalty entries
  const fetchLoyaltyEntries = async () => {
    try {
      setLoading(true);
      const token = localStorage.getItem("jwt_token");
      // NOTE: You'll need to update this endpoint to the correct one for loyalty data
      // const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty`); 
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty/`, {
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`,
        },
      });
      if (!response.ok) throw new Error(`HTTP Error ${response.status}`);
      const data = await response.json();
      // console.log(data);

      // Assuming the data is in the 'data' key of the response object
      setLoyaltyEntries(data.data || []);
    } catch (error) {
      console.error('Error fetching loyalty entries:', error);
      showErrorToast('Failed to fetch loyalty entries.');
    } finally {
      setLoading(false);
    }
  };

  // ✅ Placeholder Delete handler (Implement actual API call when needed)
  const handleDelete = async (id) => {
    const confirmed = window.confirm(`Are you sure you want to delete loyalty entry #${id}?`);
    if (!confirmed) return;

    // NOTE: Implement your actual delete logic here, similar to the Categories one.
    // For now, we'll just simulate a successful deletion and remove from state
    try {
      // const token = localStorage.getItem('jwt_token');
      // await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty/${id}`, { ... })

      showSuccessToast(`Loyalty entry #${id} deleted successfully!`);
      setLoyaltyEntries((prev) => prev.filter((entry) => entry.id !== id));
    } catch (error) {
      console.error('Error deleting loyalty entry:', error);
      showErrorToast('Error deleting loyalty entry.');
    }
  };

  // ✅ Filter & sort loyalty entries (Simplified for initial implementation)
  const filteredLoyaltyEntries = loyaltyEntries
    .filter(
      (entry) =>
        entry.id?.toString().includes(searchTerm) || // Search by ID
        entry.status?.toLowerCase().includes(searchTerm.toLowerCase()) // Search by status
    )
    .sort((a, b) => {
      // NOTE: Sorting logic needs careful implementation if created_at is '0000-00-00'
      // For now, we'll just sort by ID (latest first)
      return b.id - a.id;
    });

  // ✅ Loading UI (Copied from CategoriesIndexPage)
  if (loading) {
    return (
      <div className="!flex !justify-center !items-center !min-h-screen !bg-gradient-to-br !from-blue-50 !to-indigo-50">
        <div className="!text-center">
          <div className="!animate-spin !rounded-full !h-12 !w-12 !border-b-2 !border-blue-600 !mx-auto !mb-4"></div>
          <p className="!text-gray-600 !text-lg !font-medium">Loading loyalty entries...</p>
        </div>
      </div>
    );
  }

  // Helper to render the status badge
  const StatusBadge = ({ status }) => {
    const text = status === '0' ? 'Inactive' : status;
    const color = text === 'active'
      ? 'bg-emerald-100 text-emerald-800'
      : text === 'deactive' || text === 'Inactive'
        ? 'bg-rose-100 text-rose-800'
        : 'bg-slate-100 text-slate-800';

    return (
      <span className={`!inline-flex !items-center !rounded-full !px-3 !py-1 !text-xs !font-medium !uppercase ${color}`}>
        {text}
      </span>
    );
  };


  return (
    <div className="!max-w-7xl !mx-auto !mt-4 !p-4 !text-slate-900">
      <div className="!mb-4 !flex !items-center !justify-between">
        <h1 className="!text-2xl !font-bold !text-slate-800">Loyalty Rules</h1>
        <Link
          to="/admin/loyalty/create"
          className="!inline-flex !items-center !rounded-xl !bg-indigo-600 !px-4 !py-2 !text-sm !font-semibold !text-white !shadow-lg !shadow-indigo-500/40 !transition hover:!bg-indigo-700"
        >
          + Create Loyalty Rule
        </Link>
      </div>

      <div className="!overflow-hidden !rounded-3xl !border !border-white !bg-white !shadow-2xl !shadow-slate-200/80">
        <table className="!min-w-full !divide-y !divide-slate-100">
          <thead className="!bg-slate-50 !text-xs !uppercase !tracking-[0.2em] !text-slate-500">
            <tr>
              <th className="!px-6 !py-3 !text-left">ID</th>
              <th className="!px-6 !py-3 !text-left">Min Orders</th>
              <th className="!px-6 !py-3 !text-left">Max Orders</th>
              <th className="!px-6 !py-3 !text-left">Order Complete (%)</th>
              <th className="!px-6 !py-3 !text-left">Discount (%)</th>
              <th className="!px-6 !py-3 !text-left">Status</th>
              <th className="!px-6 !py-3 !text-left">Actions</th>
            </tr>
          </thead>
          <tbody className="!bg-white !divide-y !divide-slate-100 !text-sm">
            {filteredLoyaltyEntries.length === 0 ? (
              <tr>
                <td colSpan="7" className="!text-center !py-6 !text-slate-500">
                  No loyalty entries found.
                </td>
              </tr>
            ) : (
              filteredLoyaltyEntries.map((entry) => (
                <tr key={entry.id} className="!transition !hover:bg-slate-50">
                  <td className="!px-6 !py-4 !font-semibold !text-slate-600">#{entry.id}</td>
                  <td className="!px-6 !py-4 !text-base !font-semibold !text-slate-900">{entry.min_order}</td>
                  <td className="!px-6 !py-4 !text-base !font-semibold !text-slate-900">{entry.max_order}</td>
                  <td className="!px-6 !py-4 !text-slate-600">
                    {entry.order_complete_percentage != null ? `${entry.order_complete_percentage}%` : '—'}
                  </td>
                  <td className="!px-6 !py-4 !text-slate-600">{entry.discount}%</td>
                  <td className="!px-6 !py-4">
                    <StatusBadge status={entry.status} />
                  </td>
                  <td className="!px-6 !py-4">
                    <div className="!flex !items-center !gap-2">
                      <Link
                        to={`/admin/loyalty/edit/${entry.id}`} // Update edit path
                        title="Edit"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-emerald-300 !hover:text-emerald-600"
                      >
                        <Edit className="!h-4 !w-4" />
                      </Link>

                      <button
                        title="Delete"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-rose-300 !hover:text-rose-600"
                        onClick={() => handleDelete(entry.id)}
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

export default LoyaltyIndexPage;
