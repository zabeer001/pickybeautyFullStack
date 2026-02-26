import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { backendUrl } from '../../../env';
import { showErrorToast, showSuccessToast } from '../../utils/toast';
import { ChevronLeft } from 'lucide-react';

function LoyaltyCreatePgae() {
  const navigate = useNavigate();
  const [isSubmitting, setIsSubmitting] = useState(false);

  const [formData, setFormData] = useState({
    min_order: 0,
    max_order: 0,
    order_complete_percentage: 0,
    discount: 0,
    status: 'active',
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]:
        name === 'min_order' ||
        name === 'max_order' ||
        name === 'discount' ||
        name === 'order_complete_percentage'
          ? Number(value)
          : value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    const token = localStorage.getItem('jwt_token');

    try {
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/loyalty`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });

      const result = await response.json();

      if (response.ok && result.success) {
        showSuccessToast('Loyalty entry created successfully!');
        navigate('/admin/loyalty');
      } else {
        showErrorToast(result.message || 'Failed to create loyalty entry.');
      }
    } catch (error) {
      console.error('Error creating loyalty entry:', error);
      showErrorToast('Error communicating with the server.');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="!max-w-4xl !mx-auto !mt-4 !p-4 !text-slate-900">
      <header className="!mb-6 !flex !items-center !justify-between">
        <h1 className="!text-3xl !font-bold !text-slate-800">Create Loyalty Rule</h1>
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

          {/* Status */}
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
              {isSubmitting ? 'Creating...' : 'Create Loyalty Rule'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default LoyaltyCreatePgae
