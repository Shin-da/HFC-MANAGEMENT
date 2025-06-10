/*************  ✨ Codeium Command 🌟  *************/
import { useState } from 'react'
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { ResponsiveContainer, PieChart, Pie, Cell, BarChart, Bar, LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend } from 'recharts'
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
        ...p


/******  16d3b04e-6eb5-4ccd-a8ea-a4434406fa20  *******/