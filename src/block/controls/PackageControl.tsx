import React from 'react';
import { clsx } from 'clsx';
import { BaseController } from './BaseController';
import { RegisterOptions } from 'react-hook-form';
import type { KudosPackage } from '../../types/entity';
import { getCurrencySymbol } from '../../utils/currency';

interface PackageControlProps {
	name: string;
	packages: KudosPackage[];
	currency: string;
	isDisabled?: boolean;
	help?: React.ReactNode;
	rules?: RegisterOptions;
}

export const PackageControl = ({
	name,
	packages,
	currency,
	isDisabled,
	help,
	rules,
}: PackageControlProps) => {
	const currencySymbol = getCurrencySymbol(currency);

	return (
		<BaseController
			name={name}
			isDisabled={isDisabled}
			help={help}
			rules={rules}
			render={({ field: { onChange, value } }) => (
				<div className="first:mt-0 mt-3 grid gap-3">
					{packages.map((pkg) => {
						const selected = value === pkg.id;
						return (
							<button
								key={pkg.id}
								type="button"
								disabled={isDisabled}
								aria-pressed={selected}
								onClick={() => onChange(selected ? '' : pkg.id)}
								className={clsx(
									'control focus:ring-2 focus:ring-offset-2 focus:ring-primary text-left w-full',
									selected
										? 'bg-primary border-transparent text-white font-bold'
										: 'bg-white border-gray-300 text-slate-800 hover:bg-gray-50',
									'transition ease-in-out focus:outline-none border rounded-md py-3 px-4 flex items-center justify-between gap-3'
								)}
							>
								<span className="flex flex-col gap-1">
									<span>{pkg.title}</span>
									<span
										className={clsx(
											'text-sm font-normal',
											selected
												? 'text-white/90'
												: 'text-slate-500'
										)}
									>
										{pkg.description}
									</span>
								</span>
								<span className="shrink-0">
									{currencySymbol}
									{pkg.amount}
								</span>
							</button>
						);
					})}
				</div>
			)}
		/>
	);
};
