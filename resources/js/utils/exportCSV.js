export function exportCSV(data, filename) {
    const csvContent = data.map(row =>
      Object.values(row).map(value => `"${value}"`).join(",")
    ).join("\n");
  
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
  