import type { Metadata } from "next";
import { Inter } from "next/font/google";
// import "./globals.css";
import '@/app/style/style.css'
import {AppWrapper} from '@/context'

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "Helpii",
  description: "Generated by create next app",
};

export default function RootLayout({
  children, header, ...props
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body className="antialiased">
        <AppWrapper>
          {children}
        </AppWrapper>
      </body>
    </html>
  );
}
