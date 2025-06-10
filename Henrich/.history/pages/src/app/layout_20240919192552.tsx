import './globals.css'
import type { Metadata } from 'next'
import { Inter } from 'next/font/google'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Sales Analytics Dashboard',
  description: 'Dynamic sales analytics dashboard with prescriptive and descriptive analytics',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
/*************  ✨ Codeium Command 🌟  *************/
  return (
    <html lang="en">
      <body className={inter.className}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          {children}
        </div>
      </body>
      <body className={inter.className}>{children}</body>
    </html>
/******  ec81baf5-5922-40dd-b381-749c26f98814  *******/
  )
}