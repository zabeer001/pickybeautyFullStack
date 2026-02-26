import React, { useEffect, useState } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom'; // Ensure Link is imported
import { backendUrl } from '../../../env';
import { showErrorToast, showSuccessToast } from '../../utils/toast';
import { ChevronLeft } from 'lucide-react';

// Define the shape of a loyalty entry for type clarity
/**
 * @typedef {object} LoyaltyEntry
 * @property {number} id
 * @property {number} min_order
 * @property {number} max_order
 * @property {number} order_complete_percentage
 * @property {number} discount
 * @property {string} status
 */

function LoyaltyEditPage() {
  const { id } = useParams(); // Get ID from URL /admin/loyalty/edit/:id
  const navigate = useNavigate();

  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Form State Initialization
  const [formData, setFormData] = useState({
    min_order: 0,
    max_order: 0,
    order_complete_percentage: 0,
    discount: 0,
    status: 'active',
  });

  // 1. ✅ Fetch Initial Data
  useEffect(() => {
    const fetchLoyaltyEntry = async () => {
      try {
        setLoading(true);
         const token = localStorage.getItem("jwt_token");
       
         const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty/${id}`, {
                headers: {
                  "Content-Type": "application/json",
                  "Authorization": `Bearer ${token}`,
                },
              });
        
        if (!response.ok) {
            throw new Error(`HTTP Error ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
          const data = result.data;
          console.log(data);
          
          // Set form data with fetched values, converting any potential old '0' or 'deactive'
          // into 'inactive' for consistency in the form, if necessary.
         

          setFormData({
            min_order: data.min_order || 0,
            max_order: data.max_order || 0,
            order_complete_percentage: data.order_complete_percentage || 0,
            discount: data.discount || 0,
            status: data.status || "active" ,
          });
        } else {
            showErrorToast(result.message || 'Loyalty entry not found.');
            navigate('/admin/loyalty');
        }

      } catch (error) {
        console.error('Error fetching loyalty entry:', error);
        showErrorToast('Failed to load loyalty entry data.');
        navigate('/admin/loyalty');
      } finally {
        setLoading(false);
      }
    };

    fetchLoyaltyEntry();
  }, [id, navigate]);


  // 2. ✅ Handle Input Changes
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      // Ensure number fields are stored as numbers
      [name]:
        name === 'min_order' ||
        name === 'max_order' ||
        name === 'discount' ||
        name === 'order_complete_percentage'
          ? Number(value)
          : value,
    }));
  };

  // 3. ✅ Handle Form Submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    const token = localStorage.getItem('jwt_token');

    // Submission data is the same as the form data, assuming the backend accepts 'active'/'inactive'
    const submissionData = { ...formData };

    try {
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(submissionData),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        showSuccessToast('Loyalty entry updated successfully!');
        navigate('/admin/loyalty'); // Redirect back to the index page
      } else {
        // Display any validation or server error message
        showErrorToast(result.message || 'Failed to update loyalty entry.');
      }
    } catch (error) {
      console.error('Error updating loyalty entry:', error);
      showErrorToast('Error communicating with the server.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // ✅ Loading UI
  if (loading) {
    // ... (Loading UI is unchanged and correct)
    return (
      <div className="!flex !justify-center !items-center !min-h-screen !bg-gradient-to-br !from-blue-50 !to-indigo-50">
        <div className="!text-center">
          <div className="!animate-spin !rounded-full !h-12 !w-12 !border-b-2 !border-blue-600 !mx-auto !mb-4"></div>
          <p className="!text-gray-600 !text-lg !font-medium">Loading entry details...</p>
        </div>
      </div>
    );
  }

  // ✅ Form UI
  return (
    <div className="!max-w-4xl !mx-auto !mt-4 !p-4 !text-slate-900">
      <header className="!mb-6 !flex !items-center !justify-between">
        <h1 className="!text-3xl !font-bold !text-slate-800">
          Edit Loyalty Rule <span className="!text-indigo-600">#{id}</span>
        </h1>
        <Link
          to="/admin/loyalty"
          className="!flex !items-center !gap-1 !rounded-xl !bg-slate-100 !px-4 !py-2 !text-sm !font-medium !text-slate-600 !transition !hover:bg-slate-200"
        >
          <ChevronLeft className="!h-4 !w-4" />
          Back to List
        </Link>
      </header>

      <div className="!overflow-hidden !rounded-3xl !border !border-white !bg-white !shadow-2xl !shadow-slate-200/80 !p-8">
        <form onSubmit={handleSubmit} className="!space-y-6">
          
          {/* Min Order */}
          <div>
            <label htmlFor="min_order" className="!block !text-sm !font-medium !text-slate-700 !mb-2">
              Minimum Orders (Inclusive)
            </label>
            <input
              type="number"
              id="min_order"
              name="min_order"
              value={formData.min_order}
              onChange={handleChange}
              required
              min="0"
              className="!w-full !rounded-xl !border !border-slate-300 !p-3 !text-slate-900 !focus:ring-indigo-500 !focus:border-indigo-500 !transition"
            />
          </div>

          {/* Max Order */}
          <div>
            <label htmlFor="max_order" className="!block !text-sm !font-medium !text-slate-700 !mb-2">
              Maximum Orders (Inclusive)
            </label>
            <input
              type="number"
              id="max_order"
              name="max_order"
              value={formData.max_order}
              onChange={handleChange}
              required
              min={formData.min_order}
              className="!w-full !rounded-xl !border !border-slate-300 !p-3 !text-slate-900 !focus:ring-indigo-500 !focus:border-indigo-500 !transition"
            />
          </div>

          {/* Discount */}
          <div>
            <label htmlFor="discount" className="!block !text-sm !font-medium !text-slate-700 !mb-2">
              Discount Percentage (%)
            </label>
            <input
              type="number"
              id="discount"
              name="discount"
              value={formData.discount}
              onChange={handleChange}
              required
              min="0"
              max="100"
              className="!w-full !rounded-xl !border !border-slate-300 !p-3 !text-slate-900 !focus:ring-indigo-500 !focus:border-indigo-500 !transition"
            />
          </div>

          {/* Order Complete Percentage */}
          <div>
            <label htmlFor="order_complete_percentage" className="!block !text-sm !font-medium !text-slate-700 !mb-2">
              Order Completion Threshold (%)
            </label>
            <input
              type="number"
              id="order_complete_percentage"
              name="order_complete_percentage"
              value={formData.order_complete_percentage}
              onChange={handleChange}
              required
              min="0"
              max="100"
              className="!w-full !rounded-xl !border !border-slate-300 !p-3 !text-slate-900 !focus:ring-indigo-500 !focus:border-indigo-500 !transition"
            />
          </div>

          {/* Status - Updated Options */}
          <div>
            <label htmlFor="status" className="!block !text-sm !font-medium !text-slate-700 !mb-2">
              Status
            </label>
            <select
              id="status"
              name="status"
              value={formData.status}
              onChange={handleChange}
              className="!w-full !rounded-xl !border !border-slate-300 !p-3 !text-slate-900 !focus:ring-indigo-500 !focus:border-indigo-500 !transition !bg-white"
            >
              <option value="active">Active (Green - Visible)</option>
              <option value="inactive">Inactive (Red - Hidden)</option>
            </select>
          </div>

          {/* Submit Button */}
          <div className="!pt-4">
            <button
              type="submit"
              disabled={isSubmitting}
              className={`
                !w-full !rounded-xl !py-3 !text-lg !font-semibold !text-white !transition 
                ${isSubmitting
                  ? '!bg-indigo-400 !cursor-not-allowed'
                  : '!bg-indigo-600 !hover:bg-indigo-700 !shadow-lg !shadow-indigo-500/50'
                }
              `}
            >
              {isSubmitting ? 'Updating...' : 'Save Changes'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default LoyaltyEditPage;
