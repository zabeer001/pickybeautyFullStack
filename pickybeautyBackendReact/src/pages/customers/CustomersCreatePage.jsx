import React, { useState } from "react";
import { useNavigate } from 'react-router-dom';
import { backendUrl } from "../../../env";
import { showSuccessToast, showErrorToast } from "../../utils/toast";

function CustomersCreatePage() {
    const navigate = useNavigate();
    const token = localStorage.getItem('jwt_token');
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        phone: "",
        order_complete_count: "",
        order_cancelled_count: "",
        order_complete_percentage: "",
    });

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await fetch(`${backendUrl}/wp-json/kibsterlp-admin/v1/customers`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`,
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                const errorData = await response.json();
                showErrorToast(errorData.message || "Failed to create customer");
                return;
            }

            showSuccessToast("Customer created successfully!");
            navigate('/admin/customers');
        } catch (error) {
            showErrorToast("Something went wrong while creating the customer.");
        }
    };

    return (
        <div>
            <button
                onClick={() => navigate(-1)}
                className="!px-4 !py-2 !mb-4 !bg-black !text-white !rounded !font-bold !hover:opacity-90"
            >
                ← Back
            </button>
            <div className="!max-w-2xl !mx-auto !bg-white !shadow-sm !border !border-gray-200 !rounded-xl !p-6 !mt-8">
                <h1 className="!text-2xl !font-semibold !text-gray-800 !mb-6">
                    Create New Customer
                </h1>

                <form onSubmit={handleSubmit} className="!space-y-5">
                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            placeholder="Enter customer name"
                            value={formData.name}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                            required
                        />
                    </div>

                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            placeholder="Enter customer email"
                            value={formData.email}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                            required
                        />
                    </div>

                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Phone
                        </label>
                        <input
                            type="text"
                            name="phone"
                            placeholder="Enter customer phone"
                            value={formData.phone}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                        />
                    </div>
                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Completed Orders
                        </label>
                        <input
                            type="number"
                            name="order_complete_count"
                            placeholder="Total completed orders"
                            value={formData.order_complete_count}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                        />
                    </div>

                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Cancelled Orders
                        </label>
                        <input
                            type="number"
                            name="order_cancelled_count"
                            placeholder="Total cancelled orders"
                            value={formData.order_cancelled_count}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                        />
                    </div>

                    <div>
                        <label className="!block !text-sm !font-medium !text-gray-700 !mb-1">
                            Order Completion (%)
                        </label>
                        <input
                            type="number"
                            name="order_complete_percentage"
                            min="0"
                            max="100"
                            placeholder="Completion percentage"
                            value={formData.order_complete_percentage}
                            onChange={handleChange}
                            className="!w-full !border !border-gray-300 !rounded-lg !px-3 !py-2 !text-sm !focus:ring-2 !focus:ring-blue-500 !focus:border-blue-500"
                        />
                    </div>

                    <div className="!flex !justify-end">
                        <button
                            type="submit"
                            className="!bg-blue-600 !hover:bg-blue-700 !text-white !font-medium !px-5 !py-2 !rounded-lg !transition-all"
                        >
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

export default CustomersCreatePage;
