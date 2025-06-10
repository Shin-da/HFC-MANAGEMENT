<?php

require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>HOME</title>
    <?php require '../reusable/header.php'; ?>

    <!-- Boxicons CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>


    <!-- For Realtime Search  -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* CSS to style the buttons */
        .neworder a {
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 10px;

            cursor: pointer;
        }

        .buttonn {
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 5px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 26px;
            cursor: pointer;
        }

        .buttonn:hover {
            background-color: var(--border-color);
            color: var(--blue);
            scale: 1.1;
            transition: 0.2s ease-in-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>

</head>


<body>
    <?php
    // Alert-messages
    // include 'alerts/alert-messages.php';

    // Modals
    // include 'modals/modals.php';

    // Sidebar 
    include '../reusable/sidebar.php';

    ?>
    <!-- === Orders === -->
    <section class=" panel">
        <?php
        include '../reusable/navbar.html'; // TOP NAVBAR
        ?>


        <div class="container">
            <div class="panel-content">
                <div class="content-header">
                    <div class="title ">
                        <i class='bx bx-tachometer'></i>
                        <span class="text">Sales</span>
                    </div>
                </div>

                <div class="container" style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid var(--border-color);">
                    <!-- dito ka maglagay mike -->
                    <script>
                        import { useState } from 'react'
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { BarChart, Bar, LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend, PieChart, Pie, Cell } from 'recharts'
import { ArrowRight, TrendingUp, Lightbulb, DollarSign, ShoppingCart, Users } from "lucide-react"
import Link from "next/link"

export default function Component() {
  const [salesData, setSalesData] = useState([
    { month: 'Jan', revenue: 4000, units: 300, customers: 120 },
    { month: 'Feb', revenue: 3000, units: 250, customers: 100 },
    { month: 'Mar', revenue: 6000, units: 450, customers: 180 },
    { month: 'Apr', revenue: 8000, units: 600, customers: 220 },
    { month: 'May', revenue: 5000, units: 400, customers: 150 },
    { month: 'Jun', revenue: 7000, units: 550, customers: 200 },
  ])

  const [productPerformance, setProductPerformance] = useState([
    { name: 'Product A', value: 400 },
    { name: 'Product B', value: 300 },
    { name: 'Product C', value: 300 },
    { name: 'Product D', value: 200 },
  ])

  const [prescriptiveActions, setPrescriptiveActions] = useState([
    { action: 'Increase marketing budget for Product A', impact: 15, metric: 'Sales Growth' },
    { action: 'Implement customer loyalty program', impact: 10, metric: 'Customer Retention' },
    { action: 'Optimize pricing strategy for Product C', impact: 8, metric: 'Profit Margin' },
    { action: 'Expand distribution channels', impact: 12, metric: 'Market Reach' },
    { action: 'Launch targeted email campaign', impact: 7, metric: 'Conversion Rate' },
  ])

  const [newProduct, setNewProduct] = useState({ name: '', value: '' })

  const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8']

  const handleAddProduct = (e) => {
    e.preventDefault()
    if (newProduct.name && newProduct.value) {
      // Update product performance
      setProductPerformance([...productPerformance, { name: newProduct.name, value: parseInt(newProduct.value) }])

      // Update sales data (add to the latest month)
      const updatedSalesData = [...salesData]
      const lastMonth = updatedSalesData[updatedSalesData.length - 1]
      lastMonth.revenue += parseInt(newProduct.value)
      lastMonth.units += Math.floor(parseInt(newProduct.value) / 100) // Assuming average price of 100
      setSalesData(updatedSalesData)

      // Update prescriptive actions
      setPrescriptiveActions([
        ...prescriptiveActions,
        {
          action: `Analyze market fit for ${newProduct.name}`,
          impact: Math.floor(Math.random() * 10) + 5, // Random impact between 5-15%
          metric: 'New Product Performance'
        }
      ])

      // Reset form
      setNewProduct({ name: '', value: '' })
    }
  }
                    </script>



                </div>

            </div>

        </div>

    </section>

</body>
<script src="../resources/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    import { useState, useEffect } from 'react'

export default function Component() {
  const [salesData, setSalesData] = useState(initialSalesData)
  const [productPerformance, setProductPerformance] = useState(initialProductPerformance)
  const [prescriptiveActions, setPrescriptiveActions] = useState(initialPrescriptiveActions)

  useEffect(() => {
    // Simulating data fetch/update every 5 seconds
    const intervalId = setInterval(() => {
      // In a real application, you would fetch new data here
      // For this example, we'll just modify the existing data
      setSalesData(currentData => 
        currentData.map(item => ({...item, revenue: item.revenue * (1 + Math.random() * 0.1)}))
      )
      setProductPerformance(currentData => 
        currentData.map(item => ({...item, value: item.value * (1 + Math.random() * 0.1)}))
      )
      setPrescriptiveActions(currentData => 
        currentData.map(item => ({...item, impact: item.impact * (1 + Math.random() * 0.1)}))
      )
    }, 5000)

    return () => clearInterval(intervalId) // Cleanup on component unmount
  }, [])

  // Rest of the component code using salesData, productPerformance, and prescriptiveActions...
}
</script>

</html>