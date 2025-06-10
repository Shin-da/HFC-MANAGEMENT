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

  return (
    <div className="flex flex-col min-h-screen">
      <header className="px-4 lg:px-6 h-14 flex items-center">
        <Link className="flex items-center justify-center" href="#">
          <TrendingUp className="h-6 w-6" />
          <span className="ml-2 text-lg font-semibold">Acme Sales Analytics</span>
        </Link>
        <nav className="ml-auto flex gap-4 sm:gap-6">
          <Link className="text-sm font-medium hover:underline underline-offset-4" href="#">
            Features
          </Link>
          <Link className="text-sm font-medium hover:underline underline-offset-4" href="#">
            Pricing
          </Link>
          <Link className="text-sm font-medium hover:underline underline-offset-4" href="#">
            About
          </Link>
          <Link className="text-sm font-medium hover:underline underline-offset-4" href="#">
            Contact
          </Link>
        </nav>
      </header>
      <main className="flex-1">
        <section className="w-full py-12 md:py-24 lg:py-32 xl:py-48">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center space-y-4 text-center">
              <div className="space-y-2">
                <h1 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl lg:text-6xl/none">
                  Supercharge Your Sales with Advanced Analytics
                </h1>
                <p className="mx-auto max-w-[700px] text-gray-500 md:text-xl dark:text-gray-400">
                  Leverage the power of descriptive and prescriptive analytics to boost your sales performance and drive growth.
                </p>
              </div>
            </div>
          </div>
        </section>
        <section className="w-full py-12 md:py-24 lg:py-32 bg-gray-100 dark:bg-gray-800">
          <div className="container px-4 md:px-6">
            <div className="grid gap-6 lg:grid-cols-2 lg:gap-12">
              <Card>
                <CardHeader>
                  <CardTitle>Descriptive Sales Analytics</CardTitle>
                  <CardDescription>Understanding your sales performance</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-8">
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Sales Overview</h3>
                      <ResponsiveContainer width="100%" height={300}>
                        <LineChart data={salesData}>
                          <CartesianGrid strokeDasharray="3 3" />
                          <XAxis dataKey="month" />
                          <YAxis yAxisId="left" />
                          <YAxis yAxisId="right" orientation="right" />
                          <Tooltip />
                          <Legend />
                          <Line yAxisId="left" type="monotone" dataKey="revenue" stroke="#8884d8" name="Revenue ($)" />
                          <Line yAxisId="right" type="monotone" dataKey="units" stroke="#82ca9d" name="Units Sold" />
                        </LineChart>
                      </ResponsiveContainer>
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Product Performance</h3>
                      <ResponsiveContainer width="100%" height={300}>
                        <PieChart>
                          <Pie
                            data={productPerformance}
                            cx="50%"
                            cy="50%"
                            labelLine={false}
                            outerRadius={80}
                            fill="#8884d8"
                            dataKey="value"
                            label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                          >
                            {productPerformance.map((entry, index) => (
                              <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                            ))}
                          </Pie>
                          <Tooltip />
                        </PieChart>
                      </ResponsiveContainer>
                    </div>
                  </div>
                  <div className="mt-6 grid grid-cols-3 gap-4 text-center">
                    <div className="bg-primary/10 p-4 rounded-lg">
                      <DollarSign className="h-6 w-6 mx-auto text-primary" />
                      <div className="mt-2 text-2xl font-bold">${salesData.reduce((sum, data) => sum + data.revenue, 0).toLocaleString()}</div>
                      <div className="text-sm text-gray-500">Total Revenue</div>
                    </div>
                    <div className="bg-primary/10 p-4 rounded-lg">
                      <ShoppingCart className="h-6 w-6 mx-auto text-primary" />
                      <div className="mt-2 text-2xl font-bold">{salesData.reduce((sum, data) => sum + data.units, 0).toLocaleString()}</div>
                      <div className="text-sm text-gray-500">Units Sold</div>
                    </div>
                    <div className="bg-primary/10 p-4 rounded-lg">
                      <Users className="h-6 w-6 mx-auto text-primary" />
                      <div className="mt-2 text-2xl font-bold">{salesData.reduce((sum, data) => sum + data.customers, 0).toLocaleString()}</div>
                      <div className="text-sm text-gray-500">Total Customers</div>
                    </div>
                  </div>
                </CardContent>
              </Card>
              <Card>
                <CardHeader>
                  <CardTitle>Prescriptive Sales Analytics</CardTitle>
                  <CardDescription>Actionable insights to boost your sales</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-6">
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Recommended Actions</h3>
                      <ul className="space-y-4">
                        {prescriptiveActions.map((item, index) => (
                          <li key={index} className="bg-secondary/10 p-4 rounded-lg">
                            <div className="flex items-center space-x-2">
                              <Lightbulb className="h-5 w-5 text-yellow-500" />
                              <span className="flex-1 font-medium">{item.action}</span>
                            </div>
                            <div className="mt-2 flex justify-between text-sm">
                              <span className="text-gray-500">{item.metric}</span>
                              <span className="font-medium text-green-600">+{item.impact}% Potential Impact</span>
                            </div>
                          </li>
                        ))}
                      </ul>
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold mb-2">Impact Forecast</h3>
                      <ResponsiveContainer width="100%" height={300}>
                        <BarChart data={prescriptiveActions}>
                          <CartesianGrid strokeDasharray="3 3" />
                          <XAxis dataKey="action" angle={-45} textAnchor="end" height={100} />
                          <YAxis />
                          <Tooltip />
                          <Bar dataKey="impact" fill="#8884d8" />
                        </BarChart>
                      </ResponsiveContainer>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </section>
        <section className="w-full py-12 md:py-24 lg:py-32">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center space-y-4 text-center">
              <div className="space-y-2">
                <h2 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl">
                  Add a New Product
                </h2>
                <p className="mx-auto max-w-[600px] text-gray-500 md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed dark:text-gray-400">
                  Enter details for a new product to see how it affects your analytics.
                </p>
              </div>
              <form onSubmit={handleAddProduct} className="w-full max-w-sm space-y-4">
                <div>
                  <Label htmlFor="product-name">Product Name</Label>
                  <Input
                    id="product-name"
                    placeholder="Enter product name"
                    value={newProduct.name}
                    onChange={(e) => setNewProduct({ ...newProduct, name: e.target.value })}
                    required
                  />
                </div>
                <div>
                  <Label htmlFor="product-value">Product Value ($)</Label>
                  <Input
                    id="product-value"
                    type="number"
                    placeholder="Enter product value"
                    value={newProduct.value}
                    onChange={(e) => setNewProduct({ ...newProduct, value: e.target.value })}
                    required
                  />
                </div>
                <Button type="submit" className="w-full">Add Product</Button>
              </form>
            </div>
          </div>
        </section>
        <section className="w-full py-12 md:py-24 lg:py-32 bg-gray-100 dark:bg-gray-800">
          <div className="container px-4 md:px-6">
            <div className="flex flex-col items-center space-y-4 text-center">
              <div className="space-y-2">
                <h2 className="text-3xl font-bold tracking-tighter sm:text-4xl md:text-5xl">
                  Ready to Revolutionize Your Sales Strategy?
                </h2>
                <p className="mx-auto max-w-[600px] text-gray-500 md:text-xl/relaxed lg:text-base/relaxed xl:text-xl/relaxed dark:text-gray-400">
                  Start leveraging the power of descriptive and prescriptive analytics to make data-driven decisions and boost your sales performance.
                </p>
              </div>
              <Button className="inline-flex h-10 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50">
                Get Started
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </div>
          </div>
        </section>
      </main>
      <footer className="flex flex-col gap-2 sm:flex-row py-6 w-full shrink-0 items-center px-4 md:px-6 border-t">
        <p className="text-xs text-gray-500 dark:text-gray-400">© 2023 Acme Sales Analytics. All rights reserved.</p>
        <nav className="sm:ml-auto flex gap-4 sm:gap-6">
          <Link className="text-xs hover:underline underline-offset-4" href="#">
            Terms of Service
          </Link>
          <Link className="text-xs hover:underline underline-offset-4" href="#">
            Privacy
          </Link>
        </nav>
      </footer>
    </div>
  )
}



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