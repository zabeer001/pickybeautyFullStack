import React, { useEffect, useState } from 'react';
import { backendUrl } from '../../../env';
import { Link } from 'react-router-dom';
import { Edit, Trash } from 'lucide-react';
import { showErrorToast, showSuccessToast } from '../../utils/toast';

function CustomersIndexPage() {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const token = localStorage.getItem('jwt_token');

  useEffect(() => {
    fetchCustomers();
  }, []);

  const fetchCustomers = async () => {
    try {
      setLoading(true);
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/customers`, {
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });
      // console.log(response);
      
      if (!response.ok) throw new Error(`HTTP Error ${response.status}`);
      const data = await response.json();
      console.log(data);
      
      setCustomers(data.data || []);
    } catch (error) {
      console.error('Error fetching customers:', error);
      showErrorToast('Failed to fetch customers.');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    const confirmed = window.confirm('Are you sure you want to delete this customer?');
    if (!confirmed) return;

    try {
      const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/customers/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });

      const result = await response.json();

      if (response.ok) {
        showSuccessToast('Customer deleted successfully!');
        setCustomers((prev) => prev.filter((cust) => cust.id !== id));
      } else {
        showErrorToast(result.message || 'Failed to delete customer.');
      }
    } catch (error) {
      console.error('Error deleting customer:', error);
      showErrorToast('Error deleting customer.');
    }
  };

  if (loading) {
    return (
      <div className="!flex !justify-center !items-center !min-h-screen !bg-gradient-to-br !from-blue-50 !to-indigo-50">
        <div className="!text-center">
          <div className="!animate-spin !rounded-full !h-12 !w-12 !border-b-2 !border-blue-600 !mx-auto !mb-4"></div>
          <p className="!text-gray-600 !text-lg !font-medium">Loading customers...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="!max-w-7xl !mx-auto !mt-4 !p-4 !text-slate-900">
      <div className="!mb-4 !flex !items-center !justify-between">
        <h1 className="!text-2xl !font-bold !text-slate-800">Customers</h1>
        <Link
          to="/admin/customers/create"
          className="!inline-flex !items-center !rounded-xl !bg-indigo-600 !px-4 !py-2 !text-sm !font-semibold !text-white !shadow-lg !shadow-indigo-500/40 !transition hover:!bg-indigo-700"
        >
          + Create Customer
        </Link>
      </div>

      <div className="!overflow-hidden !rounded-3xl !border !border-white !bg-white !shadow-2xl !shadow-slate-200/80">
        <table className="!min-w-full !divide-y !divide-slate-100">
          <thead className="!bg-slate-50 !text-xs !uppercase !tracking-[0.2em] !text-slate-500">
            <tr>
              <th className="!px-6 !py-3 !text-left">ID</th>
              <th className="!px-6 !py-3 !text-left">Name</th>
              <th className="!px-6 !py-3 !text-left">Email</th>
              <th className="!px-6 !py-3 !text-left">Phone</th>
              <th className="!px-6 !py-3 !text-left">Completed</th>
              <th className="!px-6 !py-3 !text-left">Cancelled</th>
              <th className="!px-6 !py-3 !text-left">Completion %</th>
              <th className="!px-6 !py-3 !text-left">Actions</th>
            </tr>
          </thead>
          <tbody className="!bg-white !divide-y !divide-slate-100 !text-sm">
            {customers.length === 0 ? (
              <tr>
                <td colSpan="7" className="!text-center !py-6 !text-slate-500">
                  No customers found.
                </td>
              </tr>
            ) : (
              customers.map((cust) => (
                <tr key={cust.id} className="!transition !hover:bg-slate-50">
                  <td className="!px-6 !py-4 !font-semibold !text-slate-600">#{cust.id}</td>
                  <td className="!px-6 !py-4 !text-base !font-semibold !text-slate-900">{cust.name}</td>
                  <td className="!px-6 !py-4 !text-slate-600">{cust.email}</td>
                  <td className="!px-6 !py-4 !text-slate-600">{cust.phone}</td>
                  <td className="!px-6 !py-4 !text-slate-600">{cust.order_complete_count}</td>
                  <td className="!px-6 !py-4 !text-slate-600">{cust.order_cancelled_count}</td>
                  <td className="!px-6 !py-4 !text-slate-600">
                    {cust.order_complete_percentage != null ? `${cust.order_complete_percentage}%` : "-"}
                  </td>
                  <td className="!px-6 !py-4">
                    <div className="!flex !items-center !gap-2">
                      <Link
                        to={`/admin/customers/edit/${cust.id}`}
                        title="Edit"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-emerald-300 !hover:text-emerald-600"
                      >
                        <Edit className="!h-4 !w-4" />
                      </Link>

                      <button
                        title="Delete"
                        className="!rounded-2xl !border !border-slate-200 !bg-white !p-2 !text-slate-500 !transition !hover:border-rose-300 !hover:text-rose-600"
                        onClick={() => handleDelete(cust.id)}
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

export default CustomersIndexPage;
